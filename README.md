# 🏥 Pharmacy Unit System (Egypt)

A centralized platform that connects **licensed pharmacies across Egypt** in one place, helping users quickly find **unavailable or rare medicines**, while encouraging pharmacies to stock high-demand products through a **reward and scoring system**.

---

## 🎯 Problem Statement

Patients often waste time searching for unavailable medicines across multiple pharmacies.  
There was **no single trusted platform** that:
- Aggregates licensed pharmacies
- Tracks medicine availability
- Notifies users in real time when products are restocked

---

## 💡 Solution

This system provides:
- **One unified search platform** for medicines
- **Real-time availability tracking**
- **Smart incentives** for pharmacies to provide rare products

---

## ✨ Key Features

### 🔍 Product Search
- **Live Search** (instant results while typing)
- **Smart Search** (handles variations & similar product names)
- <img src="/tests/Screenshot%20(407).png" width="400" height="400" alt="Admin Dashboard"> 


### 🏪 Pharmacy Scoring & Rewards
- Pharmacies gain **scores** when users successfully find products through them
- Scores increase pharmacy ranking
- High-ranking pharmacies are eligible for **rewards**
- <img src="/tests/Screenshot%20(410).png" width="400" height="400" alt="Admin Dashboard"> 

### 🔔 Real-Time Notifications
- Users receive notifications when:
  - A requested product is restocked
- Pharmacies receive notifications when:
  - A product is highly requested
- Powered by **Laravel Reverb**
- <img src="/tests/Screenshot%20(408).png" width="400" height="400" alt="Admin Dashboard"> 


### 📊 Filament Admin Dashboard
- Manage products & pharmacies
- Filter by:
  - Most requested products
  - <img src="/tests/Screenshot%20(409).png" width="300" height="300" alt="Admin Dashboard"> 
  - Minimum stock
  - Notification demand
- Helps decision-making for product supply

### ⚙️ Performance & Scalability
- Redis caching (search, leaderboards)
- Database transactions for data consistency
- Queue system for notifications
- Distributed locks to prevent race conditions
- API throttling to prevent abuse
- Real-time leaderboards

---

## 🛠️ Tech Stack

| Layer | Technology |
|------|-----------|
| Backend | PHP 8.2, Laravel 12|
| Database | MySQL |
| Cache | Redis |
| Real-Time | Laravel Reverb |
| Queues | Laravel Queue Workers |
| Admin Panel | Filament |
| Auth & Sessions | Laravel Session Management |

---

## 📦 Installation

### 1️⃣ Clone Repository
```bash
git clone https://github.com/NadaReda22/Pharmacy.git
cd Pharmacy
```
---

👨‍💻 Author

Nada Reda Backend Developer | Problem Solving |  (2025)

[LinkedIn](https://www.linkedin.com/in/nada-reda22) | [Email](mailto:nadoarmando22@gmail.com) | [Youtube](https://www.youtube.com/watch?v=YmyozktMkIE&t=11s)
