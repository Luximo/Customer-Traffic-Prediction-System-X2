import pandas as pd
import numpy as np
from datetime import datetime, timedelta
import random
import seaborn as sns
import matplotlib.pyplot as plt
import os

# Constants
START_DATE = datetime(2023, 1, 1)
END_DATE = datetime(2023, 12, 31)
weather_conditions = {0: "Clear", 1: "Cloudy", 2: "Rainy", 3: "Snowy", 4: "Stormy"}

# Ensure plots directory exists
os.makedirs("plots", exist_ok=True)


def generate_dates(start, end):
    return [start + timedelta(days=i) for i in range((end - start).days + 1)]


def generate_traffic_data(dates):
    rows = []
    for date in dates:
        for hour in range(8, 22):  # Store open from 8 AM to 10 PM
            base = 20 + 10 * (hour in range(11, 14)) + 15 * (hour in range(16, 19))
            weekend_bonus = 20 if date.weekday() >= 5 else 0
            count = int(np.random.normal(loc=base + weekend_bonus, scale=5))
            rows.append([date.date(), hour, max(count, 0)])
    return pd.DataFrame(rows, columns=["date", "hour", "customer_count"])


def generate_sales_data(traffic_df):
    sales_rows = []
    for _, row in traffic_df.iterrows():
        total = round(row.customer_count * np.random.uniform(5.0, 25.0), 2)
        sales_rows.append([row.date, row.hour, total])
    return pd.DataFrame(sales_rows, columns=["date", "hour", "total_sales"])


def generate_weather_data(dates):
    rows = []
    for date in dates:
        temp = np.random.normal(60, 15)  # Fahrenheit
        condition = np.random.choice(
            list(weather_conditions.keys()), p=[0.5, 0.2, 0.15, 0.1, 0.05]
        )
        rows.append([date.date(), round(temp, 2), condition])
    return pd.DataFrame(rows, columns=["date", "temperature", "condition"])


def generate_promotion_data(dates):
    rows = []
    for _ in range(20):  # Generate 20 promo periods
        start = random.choice(dates)
        end = start + timedelta(days=random.randint(1, 5))
        desc = f"Promo {random.randint(100,999)}"
        rows.append([start.date(), end.date(), desc])
    return pd.DataFrame(rows, columns=["start_date", "end_date", "description"])


# === Data Generation ===
dates = generate_dates(START_DATE, END_DATE)
traffic_df = generate_traffic_data(dates)
sales_df = generate_sales_data(traffic_df)
weather_df = generate_weather_data(dates)
promo_df = generate_promotion_data(dates)

# Save CSVs
traffic_df.to_csv("traffic_data.csv", index=False)
sales_df.to_csv("sales_data.csv", index=False)
weather_df.to_csv("weather_data.csv", index=False)
promo_df.to_csv("promotion_data.csv", index=False)

print("✅ Synthetic data generated.")

# === Visualizations ===

# 1. Heatmap: Avg. traffic by hour/day
traffic_df["weekday"] = pd.to_datetime(traffic_df["date"]).dt.day_name()
pivot_table = traffic_df.pivot_table(
    index="hour", columns="weekday", values="customer_count", aggfunc="mean"
)
pivot_table = pivot_table[
    ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
]

plt.figure(figsize=(12, 6))
sns.heatmap(pivot_table, cmap="YlGnBu", annot=True, fmt=".0f")
plt.title("Average Customer Traffic (Hour vs. Weekday)")
plt.ylabel("Hour of Day")
plt.xlabel("Day of Week")
plt.tight_layout()
plt.savefig("plots/traffic_heatmap.png")
plt.close()

# 2. Line chart: Total sales per day
daily_sales = sales_df.groupby("date")["total_sales"].sum()

plt.figure(figsize=(14, 5))
daily_sales.plot()
plt.title("Total Sales Over Time")
plt.xlabel("Date")
plt.ylabel("Total Sales ($)")
plt.tight_layout()
plt.savefig("plots/sales_trend.png")
plt.close()

# 3. Bar chart: Weather condition distribution
weather_df["condition_label"] = weather_df["condition"].map(weather_conditions)

plt.figure(figsize=(8, 5))
sns.countplot(data=weather_df, x="condition_label", order=weather_conditions.values())
plt.title("Weather Condition Frequency")
plt.xlabel("Condition")
plt.ylabel("Days")
plt.tight_layout()
plt.savefig("plots/weather_conditions.png")
plt.close()

print("✅ Plots saved in 'plots/' folder.")
