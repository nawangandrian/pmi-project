#!/usr/bin/env python3
"""
predict.py - Script prediksi untuk CI4 PMI Kudus
Dipanggil oleh PrediksiController::jalankan() via shell_exec

Usage:
  python3 predict.py \
    --model       /path/model.joblib \
    --input       /path/candidates.json \
    --output      /path/results.json \
    --alpha_donor 0.2 \
    --alpha_ulang 0.1
"""

import sys
import json
import argparse
import traceback


def main():
    parser = argparse.ArgumentParser(description='Prediksi pendonor potensial')
    parser.add_argument('--model',       required=True)
    parser.add_argument('--input',       required=True)
    parser.add_argument('--output',      required=True)
    parser.add_argument('--alpha_donor', type=float, default=0.2)
    parser.add_argument('--alpha_ulang', type=float, default=0.1)
    args = parser.parse_args()

    output = {'status': 'error', 'message': '', 'hasil': []}

    try:
        import pandas as pd
        import joblib

        # ── Load model ───────────────────────────────────────────────
        pipe = joblib.load(args.model)

        # ── Load input ───────────────────────────────────────────────
        with open(args.input, 'r', encoding='utf-8') as f:
            rows = json.load(f)

        if not rows:
            output['status']  = 'empty'
            output['message'] = 'Tidak ada kandidat.'
            with open(args.output, 'w') as f:
                json.dump(output, f)
            return

        df = pd.DataFrame(rows)

        # ── Rename kolom ke nama fitur model ────────────────────────
        col_rename = {
            'status_donor'      : 'status',
            'status_pengesahan' : 'pengesahan',
            'jenis_kelamin'     : 'jk',
            'golongan_darah'    : 'gol',
            'donor_ke'          : 'donor_ke',
            'umur'              : 'umur',
        }
        df = df.rename(columns=col_rename)

        df['donor_ke']       = pd.to_numeric(df['donor_ke'],   errors='coerce').fillna(1)
        df['umur']           = pd.to_numeric(df['umur'],       errors='coerce').fillna(30)
        df['baru_ulang_num'] = df['baru_ulang'].str.lower().map({'baru': 0, 'ulang': 1}).fillna(0)

        feature_cols_cat = ['kecamatan', 'gol', 'jk', 'status', 'pengesahan']
        feature_cols_num = ['umur', 'donor_ke', 'baru_ulang_num']

        for col in feature_cols_cat + feature_cols_num:
            if col not in df.columns:
                df[col] = '' if col in feature_cols_cat else 0

        # ── Prediksi ─────────────────────────────────────────────────
        X_inf    = df[feature_cols_cat + feature_cols_num]
        p_return = pipe.predict_proba(X_inf)[:, 1]

        max_donor     = df['donor_ke'].max()
        donor_ke_norm = df['donor_ke'] / (max_donor if max_donor > 0 else 1)

        p_return_adj = (
            p_return
            + args.alpha_donor * donor_ke_norm
            + args.alpha_ulang * df['baru_ulang_num']
        )

        df['p_return_adj'] = p_return_adj

        # ── Build hasil ──────────────────────────────────────────────
        hasil = []
        for idx, row in df.iterrows():
            hasil.append({
                'id_pendonor'      : str(row.get('id_pendonor', '')),
                'nama_pendonor'    : str(row.get('nama_pendonor', '')),
                'id_pendonor_pusat': str(row.get('id_pendonor_pusat', '')),
                'kecamatan'        : str(row.get('kecamatan', '')),
                'alamat'           : str(row.get('alamat', '')),
                'no_hp'            : str(row.get('no_hp', '')),
                'umur'             : int(row.get('umur', 0)),
                'jenis_kelamin'    : str(row.get('jk', '')),
                'golongan_darah'   : str(row.get('gol', '')),
                'donor_ke'         : int(row.get('donor_ke', 0)),
                'baru_ulang'       : str(row.get('baru_ulang', '')),
                'skor'             : round(float(p_return_adj.iloc[idx if isinstance(idx, int) else df.index.get_loc(idx)]), 4),
            })

        # Sort descending skor
        hasil.sort(key=lambda x: x['skor'], reverse=True)

        output['status']  = 'success'
        output['message'] = f'{len(hasil)} kandidat diproses.'
        output['hasil']   = hasil

    except ImportError as e:
        output['message'] = f'Library tidak ditemukan: {e}. Install: pip install scikit-learn pandas joblib'
    except Exception as e:
        output['message'] = f'Error: {e}\n{traceback.format_exc()}'

    with open(args.output, 'w', encoding='utf-8') as f:
        json.dump(output, f, ensure_ascii=False)


if __name__ == '__main__':
    main()