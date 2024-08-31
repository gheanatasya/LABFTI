<template>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1; position: fixed; width: 100%;">
    </headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1; position: fixed; width: 100%;">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1; position: fixed; width: 100%;"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1; position: fixed; width: 100%;"></headerDekanat>

    <div style="padding-top: 70px;">
        <v-overlay v-model="overlay" style="background-color: white; z-index: 0">
            <v-container style="height: 660px; margin-left: 440px;">
                <v-row align-content="center" class="fill-height" justify="center">
                    <v-col class="text-subtitle-1 text-center" cols="12" style="font-family: Lexend-Regular;">
                        Memuat halaman
                    </v-col>
                    <v-col cols="6">
                        <v-progress-linear color="primary" height="6" indeterminate rounded></v-progress-linear>
                    </v-col>
                </v-row>
            </v-container>
        </v-overlay>

        <p style="font-family: Lexend-Medium; font-size: 28px; margin-left: 40px; margin-top: 20px;">Profil</p>

        <div style="width: 100%; display: flex;">
            <v-container style="width:40%; margin-left: 300px;">
                <v-icon style="font-size: 250px;">mdi-account-circle</v-icon>

                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.Nama }}
                </p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.NIM_NIDN
                    }}</p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.Role }}
                </p>

            </v-container>

            <v-container style="width:60%;">
                <v-sheet
                    style=" background-color: rgb(3, 138, 33, 0.1); font-family: Lexend-Regular; margin-right: 80px;margin-top: -50px; border-radius: 10px;">
                    <v-text-field label="Nama Lengkap" :model-value="this.user.Nama" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px; padding-top: 30px;"></v-text-field>

                    <div v-if="this.user.Role === 'Mahasiswa' || 'Petugas'">
                        <v-text-field label="NIM" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="NIDN" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <v-text-field label="Email" :model-value="this.user.Email" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px;"></v-text-field>

                    <div v-if="this.user.Role === 'Staff'">
                        <v-text-field label="Instansi" :model-value="this.user.Instansi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="Fakultas" :model-value="this.user.Fakultas" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Program Studi" :model-value="this.user.Prodi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <v-text-field label="Role" :model-value="this.user.Role" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px;"></v-text-field>

                    <v-btn @click="editprofil"
                        style="margin-top: 10px; margin-left: 420px; margin-bottom: 50px; font-family: Lexend-Medium; 
                    background-color: rgb(2, 39, 10, 0.9); color: white; width: 150px; border-radius: 20px; font-size: 17px;">Edit</v-btn>

                </v-sheet>
            </v-container>
        </div>
    </div>
</template>

<script>
import headerUser from '../components/headerUser.vue'
import headerSuperAdmin from '../components/headerSuperAdmin.vue'
import headerDekanat from '../components/headerDekanat.vue'
import headerAdmin from '../components/headerAdmin.vue'

export default {
    name: "profil",
    components: {
        headerUser,
        headerSuperAdmin,
        headerDekanat,
        headerAdmin
    },
    data() {
        return {
            user: [],
            peminjam: [],
            instansi: "",
            prodi: "",
            fakultas: "",
            loading: false,
            User_role: localStorage.getItem('User_role'),
            overlay: true,
        }
    },
    mounted() {
        Promise.all([
            this.getUserData()
        ])
            .then(() => {
                this.overlay = false;
            })
            .catch((error) => {
                console.error(error);
            });
    },
    methods: {
        async getUserData() {
            const UserID = localStorage.getItem('UserID');
            await axios.get(`http://127.0.0.1:8000/api/peminjam/getDataforProfil/${UserID}`)
                .then(response => {
                    this.user = response.data;
                    console.log(this.user);
                })
                .catch(error => {
                    console.error('Error fetching data pada tabel User', error);
                });
        },
        getProdi() {
            axios.get(`http://127.0.0.1:8000/api/prodi/getProdiNameByID/${this.peminjam.ProdiID}`)
                .then(response => {
                    this.prodi = response.data.Nama_prodi
                    const fakultasID = response.data.FakultasID
                    console.log(response.data);
                    axios.get(`http://127.0.0.1:8000/api/prodi/getFakultasNameByID/${fakultasID}`)
                        .then(res => {
                            this.fakultas = res.data.Nama_fakultas
                        })
                })
                .catch(error => {
                    console.error('Error fetching data pada tabel Prodi', error);
                })
        },
        getInstansi() {
            axios.get(`http://127.0.0.1:8000/api/instansi/getInstansiNameByID/${this.peminjam.InstansiID}`)
                .then(response => {
                    this.instansi = response.data.Nama_instansi
                })
                .catch(error => {
                    console.error('Error fetching data pada tabel Instansi', error);
                })
        },
        editprofil() {
            this.loading = true;
            this.$router.push({ name: 'editProfil' })
        }
    }
}
</script>

<style lang="scss" scoped></style>