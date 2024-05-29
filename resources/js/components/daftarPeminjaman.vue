<template>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1"></headerDekanat>

    <div id="filter" style="margin-top: 30px; margin-right: 60px;">
        <v-row style="font-family: 'Lexend-Regular';">
            <v-spacer></v-spacer>

            <v-col>
                <p style="margin-left: 200px; font-size: 20px; margin-top: 10px;">
                    <v-icon>mdi-filter-variant</v-icon>Filter
                </p>
            </v-col>

            <v-col style="margin-left: -15px;">
                <v-select label="Tanggal Pakai" variant="outlined"
                    clearable>
                </v-select>
            </v-col>

            <v-col>
                <v-select label="Action" variant="outlined"
                    clearable>
                </v-select>
            </v-col>
        </v-row>
    </div>

    <div>
        <div>Daftar Peminjaman Ruangan</div>
        
    </div>

    <div>
        <div>Daftar Peminjaman Alat</div>
    </div>

    <footerPage></footerPage>
</template>

<script>
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerAdmin from '../components/headerAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import footerPage from '../components/footerPage.vue'

export default {
    name: "daftarPeminjaman",
    components: {
        footerPage,
        headerSuperAdmin,
        headerAdmin,
        headerDekanat,
    },
    data() {
        return {
            User_role: localStorage.getItem('User_role'),
        }
    }
}
</script>

<style lang="scss" scoped></style>