#!/usr/bin/env python3
"""
train.py - Script training Random Forest untuk CI4 PMI Kudus
Dipanggil oleh ModelPrediksiController::training() via shell_exec

Usage:
  python3 train.py \
    --input   /path/input_data.json \
    --output  /path/model.joblib \
    --result  /path/result.json \
    --progress /path/progress.json \
    --cm_image  /path/confusion_matrix.png \
    --cr_image  /path/classification_report.png \
    --n_estimators    400 \
    --min_samples_leaf 2 \
    --class_weight    balanced \
    --test_size       0.2 \
    --alpha_donor     0.2 \
    --alpha_ulang     0.1 \
    --random_state    42
"""

import sys
import os
import json
import argparse
import traceback


def write_progress(path: str, persen: int, step: str, selesai: bool = False) -> None:
    """Tulis status progress ke file JSON agar bisa dibaca PHP via polling."""
    if not path:
        return
    try:
        with open(path, 'w', encoding='utf-8') as f:
            json.dump({'persen': persen, 'step': step, 'selesai': selesai}, f)
    except Exception:
        pass


def write_result(path: str, result: dict) -> None:
    """Tulis file hasil — SELALU dipanggil, bahkan saat error."""
    if not path:
        return
    try:
        with open(path, 'w', encoding='utf-8') as f:
            json.dump(result, f, ensure_ascii=False)
    except Exception as e:
        print(f"[FATAL] Gagal menulis result file '{path}': {e}", file=sys.stderr)


def save_confusion_matrix_image(cm, labels: list, output_path: str) -> bool:
    """
    Simpan Confusion Matrix sebagai file PNG.

    Parameters
    ----------
    cm          : array-like, hasil sklearn.metrics.confusion_matrix
    labels      : list nama kelas, misal ['Tidak Kembali', 'Kembali']
    output_path : str, path file PNG tujuan

    Returns
    -------
    bool — True jika berhasil, False jika gagal
    """
    try:
        import matplotlib
        matplotlib.use('Agg')          # backend non-GUI agar bisa berjalan di server
        import matplotlib.pyplot as plt
        import numpy as np

        fig, ax = plt.subplots(figsize=(6, 5))
        fig.patch.set_facecolor('#F8F9FA')
        ax.set_facecolor('#F8F9FA')

        # Heatmap manual agar tidak perlu seaborn
        im = ax.imshow(cm, interpolation='nearest', cmap='Blues')
        fig.colorbar(im, ax=ax, fraction=0.046, pad=0.04)

        # Threshold untuk warna teks
        thresh = cm.max() / 2.0
        for i in range(cm.shape[0]):
            for j in range(cm.shape[1]):
                ax.text(
                    j, i, f'{cm[i, j]:,}',
                    ha='center', va='center', fontsize=14, fontweight='bold',
                    color='white' if cm[i, j] > thresh else '#333333'
                )

        ax.set_xticks(range(len(labels)))
        ax.set_yticks(range(len(labels)))
        ax.set_xticklabels(labels, fontsize=11)
        ax.set_yticklabels(labels, fontsize=11)
        ax.set_xlabel('Prediksi', fontsize=12, labelpad=8)
        ax.set_ylabel('Aktual',   fontsize=12, labelpad=8)
        ax.set_title('Confusion Matrix', fontsize=14, fontweight='bold', pad=12)

        plt.tight_layout()
        plt.savefig(output_path, dpi=150, bbox_inches='tight',
                    facecolor=fig.get_facecolor())
        plt.close(fig)

        print(f"[INFO] Confusion matrix tersimpan: {output_path}", file=sys.stderr)
        return True

    except Exception as e:
        print(f"[WARN] Gagal menyimpan confusion matrix: {e}", file=sys.stderr)
        return False


def save_classification_report_image(report_dict: dict, output_path: str) -> bool:
    """
    Simpan Classification Report sebagai tabel PNG.

    Parameters
    ----------
    report_dict : dict, hasil sklearn.metrics.classification_report(..., output_dict=True)
    output_path : str, path file PNG tujuan

    Returns
    -------
    bool — True jika berhasil, False jika gagal
    """
    try:
        import matplotlib
        matplotlib.use('Agg')
        import matplotlib.pyplot as plt
        import numpy as np

        # ── Susun baris tabel ─────────────────────────────────────────
        # Kelas per-label + baris agregat
        AGGREGATES  = {'accuracy', 'macro avg', 'weighted avg'}
        CLASS_ROWS  = {k: v for k, v in report_dict.items()
                       if k not in AGGREGATES and isinstance(v, dict)}
        AGGREG_ROWS = {k: v for k, v in report_dict.items()
                       if k in AGGREGATES and isinstance(v, dict)}

        # Nilai akurasi (scalar)
        accuracy_val = report_dict.get('accuracy', None)

        col_headers = ['Kelas', 'Precision', 'Recall', 'F1-Score', 'Support']
        rows = []

        for label, metrics in CLASS_ROWS.items():
            rows.append([
                str(label),
                f"{metrics.get('precision', 0):.4f}",
                f"{metrics.get('recall',    0):.4f}",
                f"{metrics.get('f1-score',  0):.4f}",
                f"{int(metrics.get('support', 0)):,}",
            ])

        # Baris akurasi (hanya f1/support yang bermakna)
        if accuracy_val is not None:
            total_support = sum(
                int(v.get('support', 0))
                for v in CLASS_ROWS.values()
            )
            rows.append(['accuracy', '', '', f"{accuracy_val:.4f}", f"{total_support:,}"])

        for label, metrics in AGGREG_ROWS.items():
            rows.append([
                label,
                f"{metrics.get('precision', 0):.4f}",
                f"{metrics.get('recall',    0):.4f}",
                f"{metrics.get('f1-score',  0):.4f}",
                f"{int(metrics.get('support', 0)):,}",
            ])

        n_rows = len(rows)
        n_cols = len(col_headers)

        # ── Gambar tabel ──────────────────────────────────────────────
        fig_h = max(2.5, 0.5 + n_rows * 0.45)
        fig, ax = plt.subplots(figsize=(8, fig_h))
        fig.patch.set_facecolor('#F8F9FA')
        ax.axis('off')

        table = ax.table(
            cellText    = rows,
            colLabels   = col_headers,
            cellLoc     = 'center',
            loc         = 'center',
        )
        table.auto_set_font_size(False)
        table.set_fontsize(10)
        table.scale(1, 1.4)

        # Warna header
        HEADER_COLOR = '#2C6FAC'
        EVEN_COLOR   = '#DDEEFF'
        ODD_COLOR    = '#FFFFFF'
        AGGR_COLOR   = '#E8F0E8'

        agg_labels = {'accuracy', 'macro avg', 'weighted avg'}

        for (row_idx, col_idx), cell in table.get_celld().items():
            cell.set_edgecolor('#CCCCCC')
            if row_idx == 0:
                # Header row
                cell.set_facecolor(HEADER_COLOR)
                cell.set_text_props(color='white', fontweight='bold')
            else:
                label_val = rows[row_idx - 1][0] if row_idx - 1 < len(rows) else ''
                if label_val in agg_labels:
                    cell.set_facecolor(AGGR_COLOR)
                elif (row_idx % 2) == 0:
                    cell.set_facecolor(EVEN_COLOR)
                else:
                    cell.set_facecolor(ODD_COLOR)

        ax.set_title('Classification Report', fontsize=13, fontweight='bold',
                     pad=10, color='#222222')

        plt.tight_layout()
        plt.savefig(output_path, dpi=150, bbox_inches='tight',
                    facecolor=fig.get_facecolor())
        plt.close(fig)

        print(f"[INFO] Classification report tersimpan: {output_path}", file=sys.stderr)
        return True

    except Exception as e:
        print(f"[WARN] Gagal menyimpan classification report: {e}", file=sys.stderr)
        return False


def ensure_dir(path: str) -> None:
    """Buat direktori jika belum ada (diabaikan jika path kosong)."""
    d = os.path.dirname(path)
    if d and not os.path.isdir(d):
        os.makedirs(d, exist_ok=True)


def main():
    result = {
        'status'     : 'error',
        'message'    : 'Training tidak selesai (unknown error).',
        'akurasi'    : None,
        'f1_score'   : None,
        'roc_auc'    : None,
        'cv_roc_auc' : None,
        'total_data' : 0,
        'label_dist' : {},
        'cm_image'   : None,   # path gambar confusion matrix (jika berhasil)
        'cr_image'   : None,   # path gambar classification report (jika berhasil)
    }

    parser = argparse.ArgumentParser(description='Train Random Forest model')
    parser.add_argument('--input',            required=True,  help='Path file JSON data training')
    parser.add_argument('--output',           required=True,  help='Path output file .joblib')
    parser.add_argument('--result',           required=True,  help='Path output file JSON hasil evaluasi')
    parser.add_argument('--progress',         default='',     help='Path file JSON progress (opsional)')
    parser.add_argument('--cm_image',         default='',     help='Path output PNG confusion matrix (opsional)')
    parser.add_argument('--cr_image',         default='',     help='Path output PNG classification report (opsional)')
    parser.add_argument('--n_estimators',     type=int,   default=400)
    parser.add_argument('--min_samples_leaf', type=int,   default=2)
    parser.add_argument('--class_weight',     type=str,   default='balanced')
    parser.add_argument('--test_size',        type=float, default=0.2)
    parser.add_argument('--alpha_donor',      type=float, default=0.2)
    parser.add_argument('--alpha_ulang',      type=float, default=0.1)
    parser.add_argument('--random_state',     type=int,   default=42)

    try:
        args = parser.parse_args()
    except SystemExit as e:
        fallback = os.path.join(os.path.dirname(sys.argv[0]), 'train_result_error.json')
        result['message'] = f'Argumen tidak valid: {e}'
        write_result(fallback, result)
        sys.exit(1)

    prog = args.progress

    try:
        # ── 0. Validasi file input ────────────────────────────────────
        write_progress(prog, 2, 'Memeriksa file input...')

        if not os.path.exists(args.input):
            result['message'] = f'File input tidak ditemukan: {args.input}'
            write_progress(prog, 0, 'Error: file input tidak ada.', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        for path in (args.output, args.result, args.cm_image, args.cr_image):
            if path:
                try:
                    ensure_dir(path)
                except Exception as e:
                    print(f"[WARN] Gagal membuat direktori untuk '{path}': {e}", file=sys.stderr)

        # ── 1. Import library ─────────────────────────────────────────
        write_progress(prog, 5, 'Memuat library Python...')

        try:
            import pandas as pd
            import numpy as np
            from sklearn.model_selection import train_test_split, cross_val_score
            from sklearn.compose import ColumnTransformer
            from sklearn.preprocessing import OneHotEncoder, StandardScaler
            from sklearn.ensemble import RandomForestClassifier
            from sklearn.metrics import (
                accuracy_score, f1_score, roc_auc_score,
                confusion_matrix, classification_report,
            )
            from sklearn.pipeline import Pipeline
            import joblib
        except ImportError as e:
            result['message'] = (
                f'Library Python tidak ditemukan: {e}. '
                f'Jalankan: pip install scikit-learn pandas numpy joblib matplotlib'
            )
            write_progress(prog, 0, f'Error import: {e}', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        # ── 2. Load data ──────────────────────────────────────────────
        write_progress(prog, 10, 'Membaca data dari file...')

        with open(args.input, 'r', encoding='utf-8') as f:
            rows = json.load(f)

        if not rows:
            result['message'] = 'Data kosong, tidak ada data untuk training.'
            write_progress(prog, 0, 'Error: data kosong.', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        df = pd.DataFrame(rows)
        result['total_data'] = len(df)
        print(f"[INFO] Data dimuat: {len(df)} baris, kolom: {list(df.columns)}", file=sys.stderr)

        # ── 3. Normalisasi kolom ──────────────────────────────────────
        write_progress(prog, 20, f'Memproses {len(df):,} baris data...')

        col_rename = {
            'status_donor'      : 'status',
            'status_pengesahan' : 'pengesahan',
            'jenis_kelamin'     : 'jk',
            'golongan_darah'    : 'gol',
            'jumlah_donor'      : 'donor_ke',
            'tanggal_donor'     : 'tanggal',
        }
        col_rename = {k: v for k, v in col_rename.items() if k in df.columns}
        df = df.rename(columns=col_rename)

        df['tanggal']  = pd.to_datetime(df['tanggal'],  errors='coerce')
        df['donor_ke'] = pd.to_numeric(df.get('donor_ke', pd.Series([1]*len(df))), errors='coerce').fillna(1).astype(int)
        df['umur']     = pd.to_numeric(df.get('umur', pd.Series([30]*len(df))), errors='coerce').fillna(30).astype(int)

        if 'baru_ulang' in df.columns:
            df['baru_ulang_num'] = df['baru_ulang'].str.lower().map({'baru': 0, 'ulang': 1}).fillna(0).astype(int)
        else:
            df['baru_ulang_num'] = 0

        df = df.dropna(subset=['id_pendonor', 'tanggal'])

        if len(df) < 20:
            result['message'] = f'Data setelah normalisasi terlalu sedikit ({len(df)} baris). Minimal 20 diperlukan.'
            write_progress(prog, 0, 'Error: data terlalu sedikit.', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        # ── 4. Label ──────────────────────────────────────────────────
        write_progress(prog, 30, 'Membuat label target...')

        donor_freq        = df.groupby('id_pendonor')['tanggal'].count()
        df['total_donor'] = df['id_pendonor'].map(donor_freq)
        df['y_return']    = (df['total_donor'] >= 2).astype(int)

        label_counts         = df['y_return'].value_counts().to_dict()
        result['label_dist'] = {str(k): int(v) for k, v in label_counts.items()}
        print(f"[INFO] Distribusi label: {result['label_dist']}", file=sys.stderr)

        if len(label_counts) < 2:
            result['message'] = (
                'Data hanya memiliki satu kelas label '
                f'({result["label_dist"]}). '
                'Tambah lebih banyak data historis yang beragam.'
            )
            write_progress(prog, 0, 'Error: hanya satu kelas label.', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        # ── 5. Fitur & target ─────────────────────────────────────────
        write_progress(prog, 40, 'Menyiapkan fitur training...')

        feature_cols_cat = ['kecamatan', 'gol', 'jk', 'status', 'pengesahan']
        feature_cols_num = ['umur', 'donor_ke', 'baru_ulang_num']

        for col in feature_cols_cat:
            if col not in df.columns:
                df[col] = 'unknown'
        for col in feature_cols_num:
            if col not in df.columns:
                df[col] = 0

        for col in feature_cols_cat:
            df[col] = df[col].fillna('unknown').astype(str)
        for col in feature_cols_num:
            df[col] = pd.to_numeric(df[col], errors='coerce').fillna(0)

        X = df[feature_cols_cat + feature_cols_num].copy()
        y = df['y_return'].astype(int)

        # ── 6. Pipeline ───────────────────────────────────────────────
        write_progress(prog, 50, 'Membangun pipeline model...')

        cw = None if args.class_weight in ('None', 'none', '') else args.class_weight

        preprocess = ColumnTransformer(transformers=[
            ('cat', OneHotEncoder(handle_unknown='ignore', sparse_output=False), feature_cols_cat),
            ('num', StandardScaler(with_mean=False), feature_cols_num),
        ])

        rf = RandomForestClassifier(
            n_estimators     = args.n_estimators,
            min_samples_leaf = args.min_samples_leaf,
            class_weight     = cw,
            random_state     = args.random_state,
            n_jobs           = -1,
        )

        pipe = Pipeline([('prep', preprocess), ('rf', rf)])

        # ── 7. Train-test split & fit ─────────────────────────────────
        write_progress(prog, 60, f'Melatih Random Forest ({args.n_estimators} pohon)...')

        test_size = max(0.1, min(0.5, args.test_size))

        X_train, X_val, y_train, y_val = train_test_split(
            X, y,
            test_size    = test_size,
            stratify     = y,
            random_state = args.random_state,
        )

        print(f"[INFO] Train: {len(X_train)}, Val: {len(X_val)}", file=sys.stderr)

        pipe.fit(X_train, y_train)

        y_pred  = pipe.predict(X_val)
        y_proba = pipe.predict_proba(X_val)[:, 1]

        acc = float(round(accuracy_score(y_val, y_pred), 4))
        f1  = float(round(f1_score(y_val, y_pred, zero_division=0), 4))
        auc = float(round(roc_auc_score(y_val, y_proba), 4))

        print(f"[INFO] Akurasi: {acc}, F1: {f1}, AUC: {auc}", file=sys.stderr)

        # ── 8. Confusion Matrix & Classification Report → gambar ──────
        write_progress(prog, 75, 'Membuat visualisasi evaluasi model...')

        CLASS_LABELS = ['Tidak Kembali', 'Kembali']   # sesuai 0 / 1

        # Confusion Matrix
        cm = confusion_matrix(y_val, y_pred)
        if args.cm_image:
            ok = save_confusion_matrix_image(cm, CLASS_LABELS, args.cm_image)
            if ok:
                result['cm_image'] = args.cm_image

        # Classification Report
        cr_dict = classification_report(
            y_val, y_pred,
            target_names = CLASS_LABELS,
            zero_division = 0,
            output_dict  = True,
        )
        if args.cr_image:
            ok = save_classification_report_image(cr_dict, args.cr_image)
            if ok:
                result['cr_image'] = args.cr_image

        # ── 9. Cross-validation ───────────────────────────────────────
        write_progress(prog, 85, 'Menghitung cross-validation (5-fold)...')

        cv_scores = cross_val_score(pipe, X, y, cv=5, scoring='roc_auc', n_jobs=-1)
        cv_mean   = float(round(float(cv_scores.mean()), 4))
        print(f"[INFO] CV AUC: {cv_mean} (scores: {cv_scores})", file=sys.stderr)

        # ── 10. Simpan model ──────────────────────────────────────────
        write_progress(prog, 95, 'Menyimpan file model...')

        joblib.dump(pipe, args.output)

        if not os.path.exists(args.output):
            result['message'] = f'File model gagal tersimpan di: {args.output}'
            write_progress(prog, 0, 'Error: gagal simpan model.', selesai=True)
            write_result(args.result, result)
            sys.exit(1)

        print(f"[INFO] Model tersimpan: {args.output}", file=sys.stderr)

        result.update({
            'status'     : 'success',
            'message'    : 'Training selesai.',
            'akurasi'    : acc,
            'f1_score'   : f1,
            'roc_auc'    : auc,
            'cv_roc_auc' : cv_mean,
        })

        write_progress(prog, 100, 'Selesai!', selesai=True)

    except MemoryError:
        result['message'] = 'Memori tidak cukup untuk melatih model. Kurangi n_estimators atau jumlah data.'
        write_progress(prog, 0, 'Error: out of memory.', selesai=True)
    except Exception as e:
        tb = traceback.format_exc()
        result['message'] = f'Error: {str(e)}\n{tb}'
        write_progress(prog, 0, f'Error: {str(e)[:80]}', selesai=True)
        print(f"[ERROR] {tb}", file=sys.stderr)

    # ── SELALU tulis result file ──────────────────────────────────────
    write_result(args.result, result)

    if result['status'] != 'success':
        sys.exit(1)


if __name__ == '__main__':
    main()