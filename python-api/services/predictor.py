import os
import cv2
import numpy as np
from ultralytics import YOLO

_MODEL_PATH = os.path.join(
    os.path.dirname(os.path.dirname(os.path.abspath(__file__))),
    "model",
    "chefmate_cls_v22.pt",
)

_model: YOLO | None = None

def get_model() -> YOLO:
    """YOLO modelini belleğe bir kez yükler, sonraki çağrılarda aynı örneği döndürür."""
    global _model
    if _model is None:
        if not os.path.isfile(_MODEL_PATH):
            raise FileNotFoundError(f"Model dosyası bulunamadı: {_MODEL_PATH}")
        _model = YOLO(_MODEL_PATH)
    return _model

def predict_image(image_bytes: bytes) -> dict:
    """
    Ham resim byte dizisini alır, YOLO11n-cls ile sınıflandırır.

    Dönüş sözleşmesi (Flask API sözleşmesiyle birebir aynı):
        Başarılı: {"success": True, "food": "tomato", "confidence": 0.97}
        Başarısız: {"success": False, "message": "..."}
    """
    try:
        np_arr = np.frombuffer(image_bytes, dtype=np.uint8)
        img_bgr = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)

        if img_bgr is None:
            return {
                "success": False,
                "message": "Görüntü dosyası okunamadı veya bozuk (desteklenmeyen format olabilir).",
            }

        img_rgb = cv2.cvtColor(img_bgr, cv2.COLOR_BGR2RGB)

        model = get_model()
        results = model(img_rgb, verbose=False)

        if not results or results[0].probs is None:
            return {"success": False, "message": "Model bir tahmin üretemedi."}

        r = results[0]
        top1_index = int(r.probs.top1)

        return {
            "success": True,
            "food": r.names[top1_index],
            "confidence": round(float(r.probs.top1conf), 4),
        }

    except FileNotFoundError as exc:
        return {"success": False, "message": str(exc)}
    except Exception:
        return {"success": False, "message": "Prediction failed."}
