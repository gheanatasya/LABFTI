import { createRouter, createWebHistory } from 'vue-router'
import berandaUser from '../components/berandaUser.vue'
import daftarAlat from '../components/daftarAlat.vue'
import daftarRuangan from '../components/daftarRuangan.vue'
import daftarPetugas from '../components/daftarPetugas.vue'
import daftarPeminjaman from '../components/daftarPeminjaman.vue'
import loginPage from '../components/loginPage.vue'
import peminjamanAlat from '../components/peminjamanAlat.vue'
import peminjamanRuangan from '../components/peminjamanRuangan.vue'
import peminjamanRuanganRekomendasi from '../components/peminjamanRuanganRekomendasi.vue'
import profil from '../components/profil.vue'
import registrationPage from '../components/registrationPage.vue'
import ruangan from '../components/ruangan.vue'
import landingPage from '../components/landingPage.vue'
import editProfil from '../components/editProfil.vue'
import rekomendasiRuangan from '../components/rekomendasiRuangan.vue'
import afterRegistration from '../components/afterRegistration.vue'
import alat from '../components/alat.vue'
import resetPassword from '../components/resetPassword.vue'
import confirmNewPass from '../components/confirmNewPass.vue'

const routes = [
    {
        path: '/',
        name: 'landingPage',
        component: landingPage
    },
    {
        path: '/berandaUser',
        name: 'berandaUser',
        component: berandaUser
    },
    {
        path: '/daftarAlat',
        name: 'daftarAlat',
        component: daftarAlat
    },
    {
        path: '/daftarPetugas',
        name: 'daftarPetugas',
        component: daftarPetugas
    },
    {
        path: '/daftarPeminjaman',
        name: 'daftarPeminjaman',
        component: daftarPeminjaman
    },
    {
        path: '/loginPage',
        name: 'loginPage',
        component: loginPage
    },
    {
        path: '/peminjamanAlat',
        name: 'peminjamanAlat',
        component: peminjamanAlat
    },
    {
        path: '/peminjamanRuangan',
        name: 'peminjamanRuangan',
        component: peminjamanRuangan
    },
    {
        path: '/peminjamanRuanganRekomendasi',
        name: 'peminjamanRuanganRekomendasi',
        component: peminjamanRuanganRekomendasi
    },
    {
        path: '/profil',
        name: 'profil',
        component: profil
    },
    {
        path: '/registrationPage',
        name: 'registrationPage',
        component: registrationPage
    },
    {
        path: '/ruangan',
        name: 'ruangan',
        component: ruangan
    },
    {
        path: '/editProfil',
        name: 'editProfil',
        component: editProfil
    },
    {
        path: '/rekomendasiRuangan',
        name: 'rekomendasiRuangan',
        component: rekomendasiRuangan
    },
    {
        path: '/afterRegistration',
        name: 'afterRegistration',
        component: afterRegistration
    },
    {
        path: '/alat',
        name: 'alat',
        component: alat
    },
    {
        path: '/daftarRuangan',
        name: 'daftarRuangan',
        component: daftarRuangan
    },
    {
        path: '/resetPassword',
        name: 'resetPassword',
        component: resetPassword
    },
    {
        path: '/confirmNewPass/:token/:email',
        name: 'confirmNewPass',
        component: confirmNewPass
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;
