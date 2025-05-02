import pandas as pd
from datetime import datetime


def load_and_merge_data():
    # Load datasets
    traffic = pd.read_csv("traffic_data.csv")
    sales = pd.read_csv("sales_data.csv")
    weather = pd.read_csv("weather_data.csv")
    promos = pd.read_csv("promotion_data.csv")

    # Merge traffic + sales
    df = pd.merge(traffic, sales, on=["date", "hour"], how="left")

    # Merge weather
    df = pd.merge(df, weather, on="date", how="left")

    # Add datetime object for time features
    df["datetime"] = pd.to_datetime(df["date"]) + pd.to_timedelta(df["hour"], unit="h")
    df["weekday"] = df["datetime"].dt.weekday
    df["month"] = df["datetime"].dt.month

    # Add promotion active flag
    promos["start_date"] = pd.to_datetime(promos["start_date"])
    promos["end_date"] = pd.to_datetime(promos["end_date"])
    df["is_promo"] = df["date"].apply(
        lambda d: any(
            (pd.to_datetime(d) >= s) and (pd.to_datetime(d) <= e)
            for s, e in zip(promos["start_date"], promos["end_date"])
        )
    )

    # Drop unused
    df.drop(columns=["datetime", "date"], inplace=True)

    return df


if __name__ == "__main__":
    df = load_and_merge_data()
    print("✅ Feature set ready:")
    print(df.head())
