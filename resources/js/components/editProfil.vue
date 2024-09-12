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
                        <v-progress-linear color="#0D47A1" height="6" indeterminate rounded></v-progress-linear>
                    </v-col>
                </v-row>
            </v-container>
        </v-overlay>
        
        <p style="font-family: Lexend-Medium; font-size: 28px; margin-left: 40px; margin-top: 20px;">Profil</p>

        <div style="width: 100%; display: flex;">
            <v-container style="width:40%; margin-left: 200px;">
                <v-icon style="font-size: 250px;">mdi-account-circle</v-icon>

                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.namaNotChange
                    }} </p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.NIM_NIDN
                    }}</p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{
                    this.user.Role }}</p>
            </v-container>

            <v-container style="width:55%;">
                <v-sheet
                    style=" background-color: #BBDEFB; font-family: Lexend-Regular; margin-right: 80px;margin-top: -50px; border-radius: 10px;">
                    <v-text-field label="Nama Lengkap" v-model="this.user.Nama" variant="outlined"
                        style="margin-right: 50px; margin-left:40px; padding-top: 30px;"></v-text-field>

                    <div v-if="this.user.Role === 'Mahasiswa' || 'Petugas'">
                        <v-text-field label="NIM" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="NIDN" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>
                    </div>

                    <v-text-field label="Email" :model-value="this.user.Email" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>

                    <!-- <v-text-field label="Password" v-model="this.password" variant="outlined"
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'" :type="show ? 'text' : 'password'"
                        @click:append="show = !show" style="margin-right: 50px; margin-left:40px;"></v-text-field> -->

                    <div v-if="this.user.Role === 'Staff'">
                        <v-text-field label="Instansi" :model-value="this.user.Instansi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="Fakultas" :model-value="this.user.Fakultas" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>

                        <v-text-field label="Program Studi" :model-value="this.user.Prodi" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>
                    </div>

                    <v-text-field label="Role" :model-value="this.user.Role" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px; color: rgb(0, 0, 0, 0.5);"></v-text-field>

                    <p @click="dialogEmail(this.user.Email)"
                        style="color: #0D47A1; text-decoration: underline; cursor: pointer; margin-right: 50px; margin-left:40px;">
                        Ubah
                        email?</p>

                    <p @click="dialogPassword(this.password)"
                        style="color: #0D47A1; text-decoration: underline; cursor: pointer; margin-right: 50px; margin-left:40px;">
                        Ubah
                        password?</p>

                    <div style="display: flex; justify-content: center;">
                        <v-btn :to="'profil'"
                            style="margin-right: 20px;margin-top: 10px; margin-left: 420px; margin-bottom: 50px; font-family: Lexend-Bold; border: 3px solid #0D47A1;
                            box-shadow: none;background-color: none; color: #0D47A1; width: 150px; border-radius: 20px; font-size: 17px;">Batal</v-btn>
                        <v-btn @click="updateNama(this.user.Nama)" :loading="loading"
                            style="margin-top: 10px; margin-right: 50px; margin-bottom: 50px; font-family: Lexend-Medium; 
                        background-color: #0D47A1; color: white; width: 150px; border-radius: 20px; font-size: 17px;">Simpan</v-btn>
                    </div>
                </v-sheet>

                <!-- ubah email -->
                <v-dialog style="justify-content:center;" v-model="ubahEmail" persistent max-width="650">
                    <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px;">
                        <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                            Ubah Email</v-card-title>
                        <v-card-text style="text-align: center; margin-left: 50px;">
                            <v-text-field label="Email" v-model="this.email" variant="outlined"
                                style="margin-right: 100px; margin-left:40px;"></v-text-field>
                        </v-card-text>
                        <v-card-actions style="justify-content:center;">
                            <v-btn
                                style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;"
                                @click="ubahEmail = false">Batal</v-btn>
                            <v-btn @click="updateEmail(this.email)" :loading="loadingEmail"
                                style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;">Ubah</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

                <!-- ubah password -->
                <v-dialog style="justify-content:center;" v-model="ubahPassword" persistent max-width="650">
                    <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 700px;">
                        <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                            Ubah Password</v-card-title>
                        <v-card-text style="text-align: center; margin-left: 50px;">
                            <v-text-field label="Password" v-model="this.password" variant="outlined"
                                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'" :type="show ? 'text' : 'password'"
                                @click:append="show = !show"
                                style="margin-right: 100px; margin-left:40px;"></v-text-field>
                        </v-card-text>
                        <v-card-actions style="justify-content:center;">
                            <v-btn
                                style="background-color: #0D47A1; color: white; border-radius: 20px; width: 100px;"
                                @click="ubahPassword = false">Batal</v-btn>
                            <v-btn @click="updatePassword(this.password)" :loading="loadingPassword"
                                style="border: 3px solid #0D47A1;  box-shadow: none; background-color: none; width: 100px; color: #0D47A1; border-radius: 20px;">Ubah</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
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
            loadingEmail: false,
            loadingPassword: false,
            User_role: localStorage.getItem('User_role'),
            ubahEmail: false,
            ubahPassword: false,
            namaNotChange: null,
            overlay: true
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
                    this.namaNotChange = response.data.Nama;
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
        updateNama(nama) {
            this.loading = true;
            console.log(nama);
            if (nama === null || nama === '' || nama === undefined){
                alert('Terdapat data yang kosong!');
                this.loading = false
                return
            }

            const updatedNama = {
                nama: nama
            }

            console.log(updatedNama);

            const UserID = localStorage.getItem('UserID');

            axios.put(`http://127.0.0.1:8000/api/peminjam/${UserID}`, updatedNama)
                .then(rsp => {
                    this.loading = false;
                    this.$router.push({ name: 'profil' });
                }).catch(error => {
                    console.log('Error updating nama user:', error);
                    this.loading = false;
                })
        },
        dialogEmail(email) {
            this.ubahEmail = true;
            this.email = email;
            console.log(this.email);
        },
        dialogPassword(password) {
            this.ubahPassword = true;
            this.password = password;
            console.log(this.password);
        },
        updateEmail(email) {
            this.loadingEmail = true;
            const domain = email.split('@')[1] ? email.split('@')[1] : null;
            if (email === '' || email === null || email === undefined){
                alert('Email tidak boleh kosong!')
                this.loadingEmail = false
                return
            } else if (domain.toLowerCase() === 'ti.ukdw.ac.id' || domain.toLowerCase() === 'si.ukdw.ac.id' || domain.toLowerCase() === 'staff.ukdw.ac.id' || domain.toLowerCase() === 'students.ukdw.ac.id'){
                console.log('aman')
            } else {
                alert('Gunakan email domain UKDW');
                this.loadingEmail = false
                return
            }

            const emailUpdate = { email: email };
            const UserID = localStorage.getItem('UserID');
            axios.put(`http://127.0.0.1:8000/api/userEmail/${UserID}`, emailUpdate, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => {
                    console.log('Email updated successfully:', res.data);
                    this.loadingEmail = false;
                    this.ubahEmail = false;
                })
                .catch(error => {
                    console.error('Error updating user email:', error);
                    this.loadingEmail = false;
                });
        },
        updatePassword(password) {
            this.loadingPassword = true;
            if (password === '' || password === null || password === undefined){
                alert('Password tidak boleh kosong')
                this.loadingPassword = false
                return
            }

            const passwordUpdate = { password: password };
            const UserID = localStorage.getItem('UserID');
            axios.put(`http://127.0.0.1:8000/api/userPassword/${UserID}`, passwordUpdate, {
                withCredentials: true,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => {
                    console.log('Password updated successfully:', res.data);
                    this.loadingPassword = false;
                    this.ubahPassword = false;
                })
                .catch(error => {
                    console.error('Error updating user password:', error);
                    this.loadingPassword = false;
                });
        },
    }
}

</script>

<style lang="scss" scoped></style>