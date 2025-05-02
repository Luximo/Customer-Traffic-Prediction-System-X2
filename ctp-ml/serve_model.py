from flask import Flask, request, jsonify
import joblib
import numpy as np
import pandas as pd

app = Flask(__name__)

# Load model
model = joblib.load("model.pkl")

# Expected input features
FEATURES = [
    "hour",
    "total_sales",
    "temperature",
    "condition",
    "weekday",
    "month",
    "is_promo",
]


@app.route("/predict", methods=["POST"])
def predict():
    data = request.get_json()

    # Validate
    if not all(f in data for f in FEATURES):
        return jsonify({"error": "Missing one or more required features."}), 400

    # Prepare input for prediction
    X_input = pd.DataFrame([data], columns=FEATURES)

    # Predict
    prediction = model.predict(X_input)[0]
    return jsonify({"predicted_customer_count": round(prediction)})


@app.route("/ping", methods=["GET"])
def ping():
    return jsonify({"message": "ML service is up!"})


if __name__ == "__main__":
    app.run(port=5000, debug=True)
