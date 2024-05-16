<template>
    <headerUser v-if="User_role === 'Mahasiswa' || User_role === 'Dosen' || User_role === 'Staff'" style="z-index: 1">
    </headerUser>
    <headerSuperAdmin v-if="User_role === 'Kepala Lab' || User_role === 'Koordinator Lab'" style="z-index: 1">
    </headerSuperAdmin>
    <headerAdmin v-if="User_role === 'Petugas'" style="z-index: 1"></headerAdmin>
    <headerDekanat v-if="User_role === 'Dekan' || User_role === 'Wakil Dekan 2' || User_role === 'Wakil Dekan 3'"
        style="z-index: 1"></headerDekanat>

    <div style="margin-top: 70px;">
        <p style="font-family: Lexend-Medium; font-size: 28px; margin-left: 40px; margin-top: 20px;">Profil</p>

        <div style="width: 100%; display: flex;">
            <v-container style="width:40%; margin-left: 200px;">
                <v-icon style="font-size: 250px;">mdi-account-circle</v-icon>
                <v-icon style="font-size:30px;margin-top: 180px; margin-left: -50px;">mdi-pencil</v-icon>

                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.peminjam.Nama
                    }} </p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.NIM_NIDN
                    }}</p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{
                    this.user.User_role }}</p>

            </v-container>

            <v-container style="width:55%;">
                <v-sheet
                    style=" background-color: rgb(3, 138, 33, 0.1); font-family: Lexend-Regular; margin-right: 80px;margin-top: -50px; border-radius: 10px;">
                    <v-text-field label="Nama Lengkap" v-model="this.namaNew" :placeholder="this.peminjam.Nama"
                        variant="outlined"
                        style="margin-right: 50px; margin-left:40px; padding-top: 30px;"></v-text-field>

                    <div v-if="this.user.User_role === 'Mahasiswa' || 'Petugas'">
                        <v-text-field label="NIM" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="NIDN" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <v-text-field label="Email" v-model="this.email" :placeholder="this.user.Email" variant="outlined"
                        style="margin-right: 50px; margin-left:40px;"></v-text-field>

                    <v-text-field label="Password" v-model="this.password" variant="outlined"
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'" :type="show ? 'text' : 'password'"
                        @click:append="show = !show" style="margin-right: 50px; margin-left:40px;"></v-text-field>

                    <div v-if="this.user.User_role === 'Staff'">
                        <v-text-field label="Instansi" :model-value="this.instansi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="Fakultas" :model-value="this.fakultas" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>

                        <v-text-field label="Program Studi" :model-value="this.prodi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <v-text-field label="Role" :model-value="this.user.User_role" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px;"></v-text-field>

                    <div style="display: flex; justify-content: center;">
                        <v-btn :to="'profil'"
                            style="margin-right: 20px;margin-top: 10px; margin-left: 420px; margin-bottom: 50px; font-family: Lexend-Bold; border: 3px solid rgb(2, 39, 10, 0.9);
                            box-shadow: none;background-color: none; color: rgb(2, 39, 10, 0.9); width: 150px; border-radius: 20px; font-size: 17px;">Batal</v-btn>
                        <v-btn @click="update" :loading="loading"
                            style="margin-top: 10px; margin-right: 50px; margin-bottom: 50px; font-family: Lexend-Medium; 
                        background-color: rgb(2, 39, 10, 0.9); color: white; width: 150px; border-radius: 20px; font-size: 17px;">Simpan</v-btn>
                    </div>

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
    name: "editProfil",
    components: {
        headerUser,
        headerSuperAdmin,
        headerDekanat,
        headerAdmin
    },
    data() {
        return {
            email: '',
            password: '',
            namaNew: '',
            show: true,
            user: [],
            peminjam: [],
            instansi: "",
            prodi: "",
            fakultas: "",
            loading: false,
            User_role: localStorage.getItem('User_role'),
        }
    },
    mounted() {
        this.getUserData()
    },
    methods: {
        async getUserData() {
            const UserID = localStorage.getItem('UserID');
            await axios.get(`http://127.0.0.1:8000/api/user/${UserID}`)
                .then(response => {
                    console.log(response.data)
                    this.user = response.data
                    axios.get(`http://127.0.0.1:8000/api/peminjam/byUserID/${UserID}`)
                        .then(res => {
                            console.log(res.data)
                            this.peminjam = res.data
                            this.getProdi(),
                                this.getInstansi()
                        }).catch(error => {
                            console.error('Error fetching data pada tabel Peminjam', error);
                        })
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
        update() {
            this.loading = true;
            const updatedUserData = {
                email: this.email ? this.email : this.user.Email,
                password: this.password ? this.password : this.user.Password,
            };

            const updatedNama = {
                nama: this.namaNew ? this.namaNew : this.peminjam.Nama
            }
            const UserID = localStorage.getItem('UserID');

            // Request CSRF token first
            axios.get('http://localhost:8000/sanctum/csrf-cookie')
                .then(response => {
                    // Once CSRF token is retrieved, make the PUT request with the token included in headers
                    axios.put(`http://127.0.0.1:8000/api/user/${UserID}`, updatedUserData, {
                        withCredentials: true, // Ensure credentials are included in cross-origin requests
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(res => {
                            console.log('User data updated successfully:', res.data);
                            axios.put(`http://127.0.0.1:8000/api/peminjam/${this.peminjam.Nama}`, updatedNama)
                                .then(rsp => {
                                    this.loading = false;
                                    this.$router.push({ name: 'profil' });
                                }).catch(error => {
                                    console.log('Error updating peminjam data:', error);
                                    this.loading = false;
                                })
                        })
                        .catch(error => {
                            console.error('Error updating user data:', error);
                            this.loading = false;
                        });
                })
                .catch(error => {
                    console.log(error.reponse);
                    this.loading = false;
                });
        }


    }
}

</script>

<style lang="scss" scoped></style>