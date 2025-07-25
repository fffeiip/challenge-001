# Weapons Store Application – Pure PHP CRUD App

This is a lightweight **CRUD web application** built with **PHP 7.2**, **vanilla HTML/CSS**, and **MySQL**, with no external frameworks. It allows internal users to manage **Stores** and their inventory of **Weapons**, with full relational linking, sorting/filtering/pagination, and PDF export features.

---

## 🚀 Features

- Full **CRUD** for Stores and Weapons
- Each Weapon is linked to a Store (1:N relationship)
- List views with **server-side sorting**, **filtering**, and **pagination**
- **PDF Export** for individual Weapon spec sheets
- **Entity-to-entity navigation**: Weapon → Store, Store → Weapons
- Clean and responsive UI using vanilla HTML/CSS

---

## 📂 Project Structure


```
weapons-store-app/
├── docker/                    # Docker setup for PHP 7.2
│   ├── Dockerfile
│   └── php.ini

├── src/
│   ├── index.php              # Front-facing PHP entry points
│   ├── store.php
│   ├── weapon.php
│   ├── composer.json          # Local dependencies
│   ├── DB/                     # Database schema and seed
│       ├── schema.sql
│       └── seed.sql
│   

│   ├── Core/                  # Core components
│   │   ├── Database.php
│   │   ├── Router.php
│   │   └── PDFGenerator.php

│   ├── Interfaces/            # Interface contracts
│   │   ├── StoreRepositoryInterface.php
│   │   └── WeaponRepositoryInterface.php

│   ├── Repositories/          # Interface implementations
│   │   ├── StoreRepository.php
│   │   └── WeaponRepository.php

│   ├── Controllers/           # Handle requests and logic
│   │   ├── StoreController.php
│   │   └── WeaponController.php

│   ├── Routes/                # Routing configuration
│   │   ├── store-routes.php
│   │   └── weapon-routes.php

│   ├── Views/                 # UI templates (Vanilla HTML)
│   │   ├── store/
│   │   │   ├── index.php
│   │   │   ├── create.php
│   │   │   ├── edit.php
│   │   │   └── show.php
│   │   └── weapon/
│   │       ├── index.php
│   │       ├── create.php
│   │       ├── edit.php
│   │       └── show.php

│   └── Helpers/               # Reusable utilities
│       └── SlugGenerator.php

├── .env                       # Environment config
├── docker-compose.yml         # Docker service orchestration
├── .gitignore
└── README.md
```


---
## 📂 Prerequisites

- Docker + docker-compose
- TCP Port: 8080 (for local web)
---


## 🔧 Setup Instructions

### 1. Clone Repository
```bash
git clone https://github.com/shivragshukla/weapons-store-app.git
cd weapons-store-app
```

### 2. Start Docker
```bash
docker-compose up -d --build
```
### 3. Install Composer and autoload
```bash
docker-compose exec app sh -c "composer install && composer dump-autoload"
```

## 🌐 Access App

- Open your browser and go to:
```
http://localhost:8080/
```

Or use direct endpoints:

- `http://localhost:8080/store.php`
- `http://localhost:8080/weapon.php`

---
## 🛢️ Access Database (PHPMyAdmin)

- Open your browser and go to:
```
http://localhost:8081/
```

- `DB_USER=root`
- `DB_PASS=root`
- `DB_NAME=store_weapons`

---

## ✍️ Author

Built by Shivrag Shukla as a pure PHP 7.2 technical challenge.

---

## 🛠️ License

MIT.

---

## 📸 Screenshots


### 🖼️ Stores List
<img width="1366" height="908" alt="Store Index" src="https://github.com/user-attachments/assets/b42a6a36-47b2-4a34-a81d-d49f0b706f19" />

### 🖼️ Store Detail
<img width="1366" height="739" alt="image" src="https://github.com/user-attachments/assets/7abf00ce-e1f4-4637-9e97-9c8ce5d2198c" />

### 🖼️ Store Create/Update
<img width="1366" height="829" alt="image" src="https://github.com/user-attachments/assets/8b8bbdc1-f79b-4a72-9ba1-3042946603f4" />


### 🖼️ Weapons List
<img width="1366" height="963" alt="image" src="https://github.com/user-attachments/assets/fd7cd75e-1a22-4e59-9b57-b91ca4638aa3" />

### 🖼️ Weapon Detail
<img width="1366" height="687" alt="image" src="https://github.com/user-attachments/assets/d1fe9383-1aa1-4901-a2e9-68db7a9e578c" />


### 🖼️ Weapon Create/Update
<img width="1366" height="829" alt="image" src="https://github.com/user-attachments/assets/19f23c1d-b639-4248-b8e1-782aa6653b00" />

### 🖼️ Weapon PDF 
[weapon-2.pdf](https://github.com/user-attachments/files/21438244/weapon-2.pdf)






