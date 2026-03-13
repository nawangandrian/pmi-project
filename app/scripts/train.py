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
    --n_estimators    400 \
    --min_samples_leaf 2 \
    --class_weight    balanced \
    --test_size       0.2 \
    --alpha_donor     0.2 \
    --alpha_ulang     0.1 \
    --random_state    42
"""

import sys
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
        pass   # jangan sampai gagal write progress menghentikan training


def main():
    parser = argparse.ArgumentParser(description='Train Random Forest model')
    parser.add_argument('--input',            required=True,  help='Path file JSON data training')
    parser.add_argument('--output',           required=True,  help='Path output file .joblib')
    parser.add_argument('--result',           required=True,  help='Path output file JSON hasil evaluasi')
    parser.add_argument('--progress',         default='',     help='Path file JSON progress (opsional)')
    parser.add_argument('--n_estimators',     type=int,   default=400)
    parser.add_argument('--min_samples_leaf', type=int,   default=2)
    parser.add_argument('--class_weight',     type=str,   default='balanced')
    parser.add_argument('--test_size',        type=float, default=0.2)
    parser.add_argument('--alpha_donor',      type=float, default=0.2)
    parser.add_argument('--alpha_ulang',      type=float, default=0.1)
    parser.add_argument('--random_state',     type=int,   default=42)
    args = parser.parse_args()

    prog = args.progress   # shortcut

    result = {
        'status'     : 'error',
        'message'    : '',
        'akurasi'    : None,
        'f1_score'   : None,
        'roc_auc'    : None,
        'cv_roc_auc' : None,
        'total_data' : 0,
        'label_dist' : {},
    }

    try:
        # ── 0. Import library ─────────────────────────────────────────
        write_progress(prog, 5, 'Memuat library Python...')

        import pandas as pd
        import numpy as np
        from sklearn.model_selection import train_test_split, cross_val_score
        from sklearn.compose import ColumnTransformer
        from sklearn.preprocessing import OneHotEncoder, StandardScaler
        from sklearn.ensemble import RandomForestClassifier
        from sklearn.metrics import accuracy_score, f1_score, roc_auc_score
        from sklearn.pipeline import Pipeline
        import joblib

        # ── 1. Load data ──────────────────────────────────────────────
        write_progress(prog, 10, 'Membaca data dari file...')

        with open(args.input, 'r', encoding='utf-8') as f:
            rows = json.load(f)

        if not rows:
            result['message'] = 'Data kosong, tidak ada data untuk training.'
            with open(args.result, 'w') as f:
                json.dump(result, f)
            write_progress(prog, 0, 'Error: data kosong.', selesai=True)
            sys.exit(1)

        df = pd.DataFrame(rows)
        result['total_data'] = len(df)

        # ── 2. Normalisasi kolom ──────────────────────────────────────
        write_progress(prog, 20, f'Memproses {len(df):,} baris data...')

        col_rename = {
            'status_donor'      : 'status',
            'status_pengesahan' : 'pengesahan',
            'jenis_kelamin'     : 'jk',
            'golongan_darah'    : 'gol',
            'jumlah_donor'      : 'donor_ke',
            'tanggal_donor'     : 'tanggal',
        }
        df = df.rename(columns=col_rename)

        df['tanggal']        = pd.to_datetime(df['tanggal'], errors='coerce')
        df['donor_ke']       = pd.to_numeric(df['donor_ke'], errors='coerce').fillna(1).astype(int)
        df['umur']           = pd.to_numeric(df['umur'],     errors='coerce').fillna(30).astype(int)
        df['baru_ulang_num'] = df['baru_ulang'].str.lower().map({'baru': 0, 'ulang': 1}).fillna(0)
        df = df.dropna(subset=['id_pendonor', 'tanggal'])

        # ── 3. Label ──────────────────────────────────────────────────
        write_progress(prog, 30, 'Membuat label target...')

        donor_freq        = df.groupby('id_pendonor')['tanggal'].count()
        df['total_donor'] = df['id_pendonor'].map(donor_freq)
        df['y_return']    = (df['total_donor'] >= 2).astype(int)

        label_counts         = df['y_return'].value_counts().to_dict()
        result['label_dist'] = {str(k): int(v) for k, v in label_counts.items()}

        if len(label_counts) < 2:
            result['message'] = 'Data hanya memiliki satu kelas label. Tambah lebih banyak data historis.'
            with open(args.result, 'w') as f:
                json.dump(result, f)
            write_progress(prog, 0, 'Error: hanya satu kelas label.', selesai=True)
            sys.exit(1)

        # ── 4. Fitur & target ─────────────────────────────────────────
        write_progress(prog, 40, 'Menyiapkan fitur training...')

        feature_cols_cat = ['kecamatan', 'gol', 'jk', 'status', 'pengesahan']
        feature_cols_num = ['umur', 'donor_ke', 'baru_ulang_num']

        for col in feature_cols_cat + feature_cols_num:
            if col not in df.columns:
                df[col] = '' if col in feature_cols_cat else 0

        X = df[feature_cols_cat + feature_cols_num].copy()
        y = df['y_return'].astype(int)

        # ── 5. Pipeline ───────────────────────────────────────────────
        write_progress(prog, 50, 'Membangun pipeline model...')

        cw = None if args.class_weight == 'None' else args.class_weight
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

        # ── 6. Train-test split & fit ─────────────────────────────────
        write_progress(prog, 60, f'Melatih Random Forest ({args.n_estimators} pohon)...')

        X_train, X_val, y_train, y_val = train_test_split(
            X, y,
            test_size    = args.test_size,
            stratify     = y,
            random_state = args.random_state,
        )

        pipe.fit(X_train, y_train)

        y_pred  = pipe.predict(X_val)
        y_proba = pipe.predict_proba(X_val)[:, 1]

        acc = float(round(accuracy_score(y_val, y_pred), 4))
        f1  = float(round(f1_score(y_val, y_pred, zero_division=0), 4))
        auc = float(round(roc_auc_score(y_val, y_proba), 4))

        # ── 7. Cross-validation ───────────────────────────────────────
        write_progress(prog, 80, 'Menghitung cross-validation (5-fold)...')

        cv_scores = cross_val_score(pipe, X, y, cv=5, scoring='roc_auc')
        cv_mean   = float(round(float(cv_scores.mean()), 4))

        # ── 8. Simpan model ───────────────────────────────────────────
        write_progress(prog, 95, 'Menyimpan file model...')

        joblib.dump(pipe, args.output)

        result.update({
            'status'     : 'success',
            'message'    : 'Training selesai.',
            'akurasi'    : acc,
            'f1_score'   : f1,
            'roc_auc'    : auc,
            'cv_roc_auc' : cv_mean,
        })

        write_progress(prog, 100, 'Selesai!', selesai=True)

    except ImportError as e:
        result['message'] = f'Library Python tidak ditemukan: {str(e)}. Jalankan: pip install scikit-learn pandas numpy joblib'
        write_progress(prog, 0, f'Error: {str(e)}', selesai=True)
    except Exception as e:
        result['message'] = f'Error: {str(e)}\n{traceback.format_exc()}'
        write_progress(prog, 0, f'Error: {str(e)[:80]}', selesai=True)

    with open(args.result, 'w', encoding='utf-8') as f:
        json.dump(result, f, ensure_ascii=False)

    if result['status'] != 'success':
        sys.exit(1)


if __name__ == '__main__':
    main()