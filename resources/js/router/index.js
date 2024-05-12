import { createRouter, createWebHistory } from 'vue-router'
import berandaUser from '../components/beranda.vue'
import daftarAlat from '../components/daftarAlat.vue'
import daftarPetugas from '../components/daftarPetugas.vue'
import daftarPeminjaman from '../components/daftarPeminjaman.vue'
import loginPage from '../components/loginPage.vue'
import peminjamanAlat from '../components/peminjamanAlat.vue'
import peminjamanRuangan from '../components/peminjamanRuangan.vue'
import profil from '../components/profil.vue'
import registrationPage from '../components/registrationPage.vue'
import ruangan from '../components/ruangan.vue'
import landingPage from '../components/landingPage.vue'
import editProfil from '../components/editProfil.vue'
import rekomendasiRuangan from '../components/rekomendasiRuangan.vue'
import afterRegistration from '../components/afterRegistration.vue'
import alat from '../components/alat.vue'



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
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;
