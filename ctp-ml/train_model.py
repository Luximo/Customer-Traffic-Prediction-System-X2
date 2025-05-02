import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split
from sklearn.metrics import mean_absolute_error, mean_squared_error
import joblib
import numpy as np
from feature_pipeline import load_and_merge_data

# Load processed features
df = load_and_merge_data()

# Define input features and target
features = [
    "hour",
    "total_sales",
    "temperature",
    "condition",
    "weekday",
    "month",
    "is_promo",
]
target = "customer_count"

X = df[features]
y = df[target]

# One-hot encode is_promo and condition if necessary
X["is_promo"] = X["is_promo"].astype(int)

# Train/Validation split
X_train, X_val, y_train, y_val = train_test_split(X, y, test_size=0.2, random_state=42)

# Train model
model = RandomForestRegressor(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# Predict on validation set
y_pred = model.predict(X_val)

# Evaluation
mae = mean_absolute_error(y_val, y_pred)
rmse = np.sqrt(mean_squared_error(y_val, y_pred))

print(f"✅ Model trained.")
print(f"📊 MAE:  {mae:.2f}")
print(f"📊 RMSE: {rmse:.2f}")

# Save model
joblib.dump(model, "model.pkl")
print("💾 Model saved as model.pkl")
