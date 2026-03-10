# 🚀 Deploy E-Canteen ke Railway (Gratis & Permanent URL)

**Tanggal**: 3 Maret 2026  
**Platform**: Railway.app  
**Status URL**: Permanent (tidak berubah selamanya)  
**Biaya**: **GRATIS** (500 jam/bulan gratis)

---

## 🎯 Kenapa Railway?

✅ **URL Permanent** - Sekali deploy, URL tidak berubah  
✅ **Gratis** - 500 jam/bulan (cukup untuk 1 project 24/7)  
✅ **Auto Deploy** - Push ke GitHub → Auto deploy  
✅ **Database MySQL** - Gratis included  
✅ **Laravel Support** - Optimized untuk Laravel  
✅ **HTTPS** - SSL certificate gratis  
✅ **24/7 Online** - Server tidak pernah mati

---

## 📋 Persiapan (Di Laptop)

### 1. Install Git (Jika Belum)

**Download:** https://git-scm.com/download/win

**Verify:**
```bash
git --version
```

### 2. Buat Akun GitHub (Jika Belum)

**Link:** https://github.com/signup

### 3. Buat Repository GitHub

1. Login ke GitHub
2. Klik tombol **"New"** (repository baru)
3. Isi:
   - **Repository name**: `kantinku`
   - **Description**: E-Canteen dengan QRIS Payment Gateway
   - **Visibility**: Private (atau Public)
4. **JANGAN** centang "Add README" (karena project sudah ada)
5. Klik **"Create repository"**

---

## 🔧 Setup Git di Project

Buka terminal di folder project:

```bash
cd C:/laragon/www/kantinku
```

### 1. Initialize Git

```bash
git init
git add .
git commit -m "Initial commit - E-Canteen dengan Xoftware QRIS"
```

### 2. Connect ke GitHub

**Ganti `USERNAME` dan `REPO` dengan punya Anda:**

```bash
git remote add origin https://github.com/USERNAME/kantinku.git
git branch -M main
git push -u origin main
```

**Contoh:**
```bash
git remote add origin https://github.com/johnsmith/kantinku.git
```

**Login Prompt:**
- Username: [GitHub username Anda]
- Password: [GitHub personal access token - bukan password biasa]

**Cara Buat Token:**
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token → Beri nama "Railway Deploy"
3. Centang scope: **repo** (all)
4. Copy token yang muncul (simpan baik-baik, hanya muncul sekali)

---

## 🚂 Deploy ke Railway

### 1. Buat Akun Railway

**Link:** https://railway.app/

**Sign Up dengan:**
- ✅ GitHub account (REKOMENDASI - paling mudah)
- Atau email

### 2. Create New Project

1. Login ke Railway Dashboard
2. Klik **"New Project"**
3. Pilih **"Deploy from GitHub repo"**
4. Pilih repository **`kantinku`**
5. Railway akan otomatis detect sebagai Laravel project

### 3. Setup Database MySQL

**Di Railway Dashboard:**

1. Klik project yang baru dibuat
2. Klik **"+ New"** → **"Database"** → **"Add MySQL"**
3. Railway akan create MySQL database otomatis
4. Database akan auto-connect ke aplikasi Laravel

### 4. Setup Environment Variables

**Di Railway Project → Variables tab:**

Tambahkan semua variable ini:

```env
APP_NAME=KantinKu
APP_ENV=production
APP_KEY=base64:zJyCwZwfHKG8ejeQ+wszo/Y4KSfmQO8ZpDTBKwebFcA=
APP_DEBUG=false
APP_URL=https://kantinku-production.up.railway.app

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

XOFTWARE_API_KEY=OTaX_sSz3QBfc5lK6DL-s4Km47mqEvZmFmmSK-6Jh_0
XOFTWARE_MERCHANT_ID=19
XOFTWARE_BASE_URL=https://payment.xoftware.id/v1/api
XOFTWARE_NOTIFY_URL=https://kantinku-production.up.railway.app/webhook/xoftware
```

**⚠️ PENTING:**
- `APP_URL` dan `XOFTWARE_NOTIFY_URL` ganti dengan URL Railway Anda
- URL Railway formatnya: `https://[nama-project]-production.up.railway.app`
- Bisa dicek di Railway Dashboard → Settings → Domains

**Cara Ganti APP_KEY Baru (di terminal lokal):**
```bash
php artisan key:generate --show
```
Copy hasilnya, paste ke Railway `APP_KEY`

### 5. Deploy!

**Otomatis Deploy:**
- Railway otomatis build & deploy setelah semua setup
- Tunggu 2-5 menit untuk build pertama kali
- Cek status di **"Deployments"** tab

**Status Deploy:**
- 🟡 Building... → Sedang build
- 🟢 Success → Deploy berhasil!
- 🔴 Failed → Ada error (cek logs)

---

## 🔗 Dapatkan URL Production

**Di Railway Dashboard:**

1. Klik project → Service → **Settings**
2. Scroll ke **Networking** section
3. Klik **"Generate Domain"**
4. Railway akan generate URL seperti:
   ```
   https://kantinku-production.up.railway.app
   ```
5. **URL INI PERMANENT** - tidak akan berubah!

---

## 🔄 Update Xoftware Integration

Setelah dapat URL Railway:

### 1. Update di Railway Environment

**Railway Dashboard → Variables:**

Update variable:
```env
APP_URL=https://kantinku-production.up.railway.app
XOFTWARE_NOTIFY_URL=https://kantinku-production.up.railway.app/webhook/xoftware
```

Klik **"Redeploy"** setelah update.

### 2. Update di Xoftware Dashboard

**Login ke:** https://dashboard.xoftware.id

**Option A - Jika belum approved:**
- Delete submission lama (yang pakai ngrok URL)
- Submit baru dengan URL Railway

**Option B - Jika sudah approved:**
- Hubungi Xoftware support untuk update webhook URL
- Atau submit integration baru

**Form Integration:**
- **Jenis Aplikasi**: Web
- **URL / Domain**: `https://kantinku-production.up.railway.app`
- **Deskripsi**: [Gunakan template yang sama seperti sebelumnya]

---

## ✅ Testing Deployment

### 1. Test Aplikasi Berjalan

Buka di browser:
```
https://kantinku-production.up.railway.app
```

Harus muncul halaman welcome Laravel.

### 2. Test Login

```
https://kantinku-production.up.railway.app/login
```

Login dengan akun yang sudah ada.

### 3. Test Webhook

```bash
curl https://kantinku-production.up.railway.app/webhook/xoftware
```

**Expected Response:**
```json
{"status":"ok","webhook":"xoftware"}
```

### 4. Test Top Up Flow

1. Login → Dashboard
2. Klik "Top Up"
3. Isi nominal
4. Klik "Buat QRIS & Bayar"
5. Verify QRIS page muncul

---

## 🔧 Update Code di Production

**Setiap kali ada perubahan code:**

```bash
# Di terminal lokal
git add .
git commit -m "Update: deskripsi perubahan"
git push origin main
```

Railway akan **otomatis detect push** dan **auto-deploy** dalam 2-3 menit!

---

## 📊 Monitoring & Logs

### View Logs di Railway

**Railway Dashboard → Service → Logs**

Lihat real-time logs aplikasi:
- HTTP requests
- Database queries
- Errors
- Xoftware API calls
- Webhook activities

### View Deployment Status

**Railway Dashboard → Deployments tab**

Lihat history semua deployment:
- Build time
- Deploy status
- Commit yang di-deploy

---

## 💰 Biaya Railway

### Free Plan (Default)

✅ **500 execution hours/month** (gratis)  
✅ **8 GB RAM**  
✅ **100 GB outbound bandwidth**  
✅ **MySQL database included**

**Cukup untuk:**
- 1 project Laravel 24/7
- Testing & development
- Low-medium traffic

**Kalau mau lebih:**
- Upgrade ke **Hobby Plan** ($5/month)
- Unlimited hours
- Priority support

---

## 🎯 Workflow Setelah Deploy

### Development (Lokal)

```
Laptop nyala
  ↓
Coding di VSCode
  ↓
Test di http://127.0.0.1:8000
  ↓
Git commit & push
  ↓
Railway auto-deploy
```

### Production (Railway)

```
Railway server 24/7
  ↓
URL: https://kantinku-production.up.railway.app
  ↓
User akses dari mana saja
  ↓
Xoftware webhook langsung ke Railway
  ↓
Balance terupdate otomatis
```

---

## 🚨 Troubleshooting

### Deploy Failed

**Cek di Railway Logs:**
```
Error: Class 'PDO' not found
```
→ Butuh PHP extension, add ke `nixpacks.toml`

**Solusi:**
Update `nixpacks.toml`, tambah extensions yang dibutuhkan.

### Database Connection Error

**Error:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Solusi:**
- Verify MySQL service running di Railway
- Cek environment variables `DB_*` sudah benar
- Restart deployment

### Webhook Not Working

**Error 404 di webhook:**

**Solusi:**
1. Verify route `/webhook/xoftware` ada di `routes/web.php`
2. Clear cache: Deploy ulang di Railway
3. Test dengan curl

---

## 🎉 Success Checklist

Setelah deploy berhasil:

- [ ] ✅ URL Railway accessible
- [ ] ✅ Aplikasi bisa login
- [ ] ✅ Dashboard load dengan benar
- [ ] ✅ Database connection OK
- [ ] ✅ Top-up form accessible
- [ ] ✅ Webhook endpoint respond JSON
- [ ] ✅ Xoftware integration updated dengan URL Railway
- [ ] ✅ Merchant approval diterima
- [ ] ✅ QRIS generation berhasil
- [ ] ✅ Payment test sukses
- [ ] ✅ Balance auto-update via webhook

---

## 🔗 Quick Links

- **Railway Dashboard**: https://railway.app/dashboard
- **Xoftware Dashboard**: https://dashboard.xoftware.id
- **Your Production URL**: `https://kantinku-production.up.railway.app`
- **GitHub Repository**: `https://github.com/USERNAME/kantinku`

---

## 📞 Support

**Railway Support:**
- Docs: https://docs.railway.app
- Discord: https://discord.gg/railway

**Xoftware Support:**
- Email: support@xoftware.id
- Dashboard: https://dashboard.xoftware.id

---

## 🎯 Next Steps

1. ✅ Setup Git & GitHub
2. ✅ Push code ke GitHub
3. ✅ Deploy ke Railway
4. ✅ Setup MySQL database
5. ✅ Configure environment variables
6. ✅ Generate Railway domain
7. ✅ Update Xoftware integration
8. ✅ Test full payment flow
9. ✅ Monitor logs
10. 🚀 **GO LIVE!**

---

**🎊 Selamat! Aplikasi Anda sekarang online 24/7 dengan URL permanent!**

**Tidak perlu:**
- ❌ Laptop nyala terus
- ❌ Ngrok
- ❌ URL yang berubah-ubah
- ❌ Manual restart

**Cukup:**
- ✅ Push code
- ✅ Railway auto-deploy
- ✅ Online selamanya!
