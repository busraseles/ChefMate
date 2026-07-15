"""
app.py
======
ChefMate Flask API — HTTP giriş noktası.

Bu dosya BİLİNÇLİ OLARAK "ince" tutulmuştur: YOLO/OpenCV kodu burada
YOKTUR, hepsi services/predictor.py içindedir. app.py'nin tek görevi:
  1) HTTP isteğini karşılamak (dosya var mı, uzantı geçerli mi?)
  2) Gelen resmi (audit/debug amaçlı) uploads/ klasörüne kaydetmek
  3) predictor.predict_image()'ı çağırıp sonucu JSON olarak döndürmek

Eski mimaride PHP, `shell_exec()` ile bu script'i doğrudan process
olarak çalıştırıyordu. Yeni mimaride PHP, bu dosyayı HTTP POST ile
çağırır — Flask bağımsız bir servis olarak sürekli ayakta durur ve
modeli sadece bir kez belleğe yükler (bkz. services/predictor.py).
"""

import os
import uuid
from datetime import datetime

from flask import Flask, request, jsonify
from werkzeug.utils import secure_filename

from services.predictor import predict_image

app = Flask(__name__)

UPLOAD_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "uploads")
ALLOWED_EXTENSIONS = {"jpg", "jpeg", "png", "webp", "bmp"}

os.makedirs(UPLOAD_DIR, exist_ok=True)

def _is_allowed(filename: str) -> bool:
    return "." in filename and filename.rsplit(".", 1)[1].lower() in ALLOWED_EXTENSIONS

def _save_for_audit(filename: str, image_bytes: bytes) -> str:
    """
    Gelen görüntüyü uploads/ klasörüne kaydeder (denetim/hata ayıklama amaçlı).
    Not: Tahmin işlemi bu dosyadan DEĞİL, doğrudan bellekteki byte'lardan
    yapılır (predictor.predict_image) — disk I/O tahmini yavaşlatmaz.
    """
    safe_name = secure_filename(filename) or "upload"
    ext = safe_name.rsplit(".", 1)[-1] if "." in safe_name else "jpg"
    stamped_name = f"{datetime.now():%Y%m%d_%H%M%S}_{uuid.uuid4().hex[:8]}.{ext}"
    target_path = os.path.join(UPLOAD_DIR, stamped_name)

    with open(target_path, "wb") as f:
        f.write(image_bytes)

    return target_path

@app.route("/predict", methods=["POST"])
def predict():
    if "image" not in request.files:
        return jsonify({"success": False, "message": "İstekte 'image' alanı bulunamadı."}), 400

    file = request.files["image"]

    if file.filename == "":
        return jsonify({"success": False, "message": "Dosya seçilmedi."}), 400

    if not _is_allowed(file.filename):
        return jsonify({"success": False, "message": "Desteklenmeyen dosya türü."}), 400

    image_bytes = file.read()

    if not image_bytes:
        return jsonify({"success": False, "message": "Gönderilen dosya boş."}), 400

    try:
        _save_for_audit(file.filename, image_bytes)
    except OSError:
        pass

    result = predict_image(image_bytes)
    status_code = 200 if result.get("success") else 500

    return jsonify(result), status_code

@app.route("/health", methods=["GET"])
def health():
    """PHP tarafının 'Flask ayakta mı?' kontrolü için basit sağlık kontrolü."""
    return jsonify({"status": "ok"}), 200

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
