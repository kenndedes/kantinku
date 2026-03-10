# 🚀 Quick Deploy Guide - Railway

## TL;DR (Super Cepat)

1. **Setup Git & GitHub** (5 menit)

    ```bash
    git init
    git add .
    git commit -m "Initial commit"
    git remote add origin https://github.com/USERNAME/kantinku.git
    git push -u origin main
    ```

2. **Deploy ke Railway** (10 menit)
    - Buka https://railway.app
    - Sign up dengan GitHub
    - New Project → Deploy from GitHub → Pilih `kantinku`
    - Add MySQL database
    - Set environment variables
    - Generate domain

3. **Update Xoftware** (5 menit)
    - Buka https://dashboard.xoftware.id
    - Submit integration dengan URL Railway
    - Tunggu approval

4. **SELESAI!** 🎉
    - URL permanent: `https://your-app.up.railway.app`
    - Online 24/7
    - Tidak perlu laptop nyala
    - Tidak perlu ngrok lagi

---

## 📁 Files untuk Railway

Sudah dibuat:

- ✅ `Procfile` - Railway start command
- ✅ `nixpacks.toml` - Build configuration
- ✅ `.gitignore` - Exclude .env dari Git
- ✅ `.env.example` - Template environment

---

## 🔗 Links Penting

- **Railway**: https://railway.app
- **GitHub**: https://github.com
- **Xoftware**: https://dashboard.xoftware.id
- **Panduan Lengkap**: Baca `DEPLOY_RAILWAY.md`

---

## ⚡ Benefits Railway

| Sebelum (Ngrok)               | Setelah (Railway)    |
| ----------------------------- | -------------------- |
| ❌ URL berubah setiap restart | ✅ URL permanent     |
| ❌ Laptop harus nyala 24/7    | ✅ Cloud server 24/7 |
| ❌ Restart = setup ulang      | ✅ Always online     |
| ❌ Xoftware re-approval       | ✅ Sekali approval   |
| ❌ Tidak stabil               | ✅ Production-ready  |

---

## 💵 Biaya

✅ **GRATIS** - 500 jam/bulan (cukup untuk 1 app 24/7)

---

**Need help?** Baca `DEPLOY_RAILWAY.md` untuk panduan detailStep-by-step! 📖
