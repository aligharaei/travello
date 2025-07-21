# 📄 NOTES.md

**GitHub Repository:**  
🔗 [https://github.com/aligharaei/travello.git](https://github.com/aligharaei/travello.git)

**API Testing Environment:**  
🔗 I used [Apidog](https://lbd3vc80zx.apidog.io) instead of Swagger to test and explore the API efficiently.
[https://lbd3vc80zx.apidog.io](https://lbd3vc80zx.apidog.io)
---

## ✅ Overview
This implementation integrates the **Heavenly Tours API** into the existing Laravel backend in a clean and extensible way. All responses are normalized to match the internal API format, so consumers won’t need to care where the data comes from.

---

## 🧱 Key Points

- Created a `TourProviderInterface` to define a consistent contract for providers.
- Implemented `HeavenlyTourProvider` with proper normalization, caching, and retry logic.
- Controllers rely only on the interface — no need to change them when switching or adding providers.
- Currency and availability values are localized using translation files.

---

## 🚀 Available Endpoints

- `GET /api/v1/tours`
- `GET /api/v1/tours/{id}`
- `GET /api/v1/tours/{id}/availability`
- `GET /api/v1/tours/prices`  
  Supports pagination via `?limit=` and `?page=`.

---

## 💡 Notes

- Temporary workaround added for SSL issues using `withoutVerifying()`.
- The code is designed to support future providers with minimal changes.
- Retry and caching improve reliability and reduce load.
