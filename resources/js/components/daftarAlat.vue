<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1"></headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>

    <div style="font-family: 'Lexend-Medium'; font-size: 25px; text-align: center;"> Daftar Alat LAB FTI UKDW </div>
    <div>
        
    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerAdmin from '../components/headerAdmin.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "daftarAlat",
    components: {
        footerPage,
        headerSuperAdmin,
        headerAdmin
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
        }
    },

}
</script>

<style lang="scss" scoped></style>