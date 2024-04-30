<template>
    <div style="height: 100%; width: 100%; display: flex;">
        <v-container style="height: 100%; width: 38%; margin: 0; padding: 0;">
            <v-img src="../picture/regis-login.jpeg" style="height: 100%;"></v-img>
        </v-container>

        <v-container style="height: 100%; width: 72%;">
            <div style="display: flex; flex-direction: column;">
                <router-link to="/loginPage"><v-icon
                        style="font-size: 30px;">mdi-keyboard-backspace</v-icon></router-link>
                <div style="font-family: Lexend-Medium; font-size: 25px; margin-left: 380px; margin-bottom:25px;">Buat
                    Akun Baru</div>

                <v-form @submit.prevent="addUser" method="post">
                    <v-sheet
                        style="margin-left: 100px; display: grid; grid-template-columns: 1fr 1fr; grid-gap: 40px; font-family: Lexend-Regular;">
                        <v-text-field type="input" variant="outlined" v-model="name" label="Nama Lengkap"
                            style="grid-column: span 2; height: 30px; margin-right: 450px;"></v-text-field>
                        <v-text-field type="input" variant="outlined" v-model="NIM_NIDN" label="NIM / NIDN"
                            style="grid-column: span 2; height: 30px; margin-right: 500px;"></v-text-field>
                        <v-text-field variant="outlined" v-model="email" label="Email" type="email" :rules="emailRules"
                            style="grid-column: span 2; height: 50px; margin-right: 450px;"></v-text-field>

                        <div style="display: flex; align-items: center; grid-column: span 2; margin-right: 150px;">
                            <v-text-field variant="outlined" v-model="password" label="Password"
                                style="margin-right: 20px; height: 80px;"
                                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'" :type="show ? 'text' : 'password'"
                                @click:append="show = !show"></v-text-field>
                            <v-text-field variant="outlined" v-model="konfpass" label="Konfirmasi Password"
                                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :type="show1 ? 'text' : 'password'"
                                @click:append="show1 = !show1" :error-messages="konfpassErrorMessages"
                                style="height: 80px;"></v-text-field>
                        </div>

                        <div
                            style="display: flex; align-items: center; grid-column: span 2; margin-right: 150px; margin-top: -40px;">
                            <v-select variant="outlined" label="Fakultas" :items="Fakultas" v-model="selectedFakultas"
                                style="margin-right: 20px; height: 30px;"></v-select>
                            <v-select variant="outlined" v-model="selectedProdi" label="Prodi" :items="Prodi"
                                style="height: 30px;"></v-select>
                        </div>
                        <v-select variant="outlined" v-model="selectedInstansi" label="Instansi" :items="Instansi"
                            style="margin-right: 20px; height: 30px;"></v-select>
                        <v-spacer></v-spacer>
                        <v-text-field variant="outlined" v-model="this.user_role" label="Role" :disabled="true"
                            style="grid-column: span 1; height: 50px; margin-right: 50px;"></v-text-field>
                    </v-sheet>
                    <v-btn @click="addUser" :loading="loading" type="submit"
                        style="margin-top: 30px; margin-left: 330px; font-family: Lexend-Medium; background-color: rgb(2, 39, 10, 0.9); color: white; width: 300px; border-radius: 20px; font-size: 17px;">
                        <span v-if="!loading">Buat Akun</span>
                        <span v-else>Loading...</span>
                    </v-btn>
                </v-form>
            </div>

            <div>
                <p style="margin-top: 30px; margin-left: 100px;font-family:Lexend-Regular;">Sudah memiliki akun?
                    <router-link style="color: rgb(2, 39, 10, 0.9); font-family: Lexend-Bold"
                        to="/loginPage">Login</router-link>
                </p>
            </div>
        </v-container>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: "registrationPage",
    data() {
        return {
            loading: false,
            show: true,
            show1: true,
            konfpassErrorMessages: [],
            name: "",
            NIM_NIDN: "",
            email: "",
            password: "",
            konfpass: "",
            user_role: "",
            user_priority: "",
            Fakultas: [],
            Prodi: [],
            Instansi: [],
            selectedFakultasID: "",
            selectedProdiID: "",
            selectedInstansiID: "",
            selectedFakultas: "Lainnya",
            selectedProdi: "Lainnya",
            selectedInstansi: "Lainnya",
            fakultasID: [],
            data: undefined,
            user: {},
            emailRules: [
                v => !!v || 'Gunakan email dengan domain UKDW',
                v => {
                    const validDomains = ['ti.ukdw.ac.id', 'si.ukdw.ac.id', 'staff.ukdw.ac.id', 'students.ukdw.ac.id'];
                    const domain = v.split('@')[1];
                    return validDomains.includes(domain) || 'Silahkan gunakan email domain UKDW';
                }
            ],
        }
    },
    mounted() {
        this.fetchFakultas(),
            this.fetchProdi(),
            this.fetchInstansi()
    },
    watch: {
        email: function (newEmail) {
            this.setRole();
        },
        nim: function (newNim) {
            this.setRole();
        },
        selectedInstansi: function (newInstansi) {
            this.setRole();
        },
        selectedFakultas: function (newFakultas) {
            this.fetchProdiByFakultas();
            this.setRole();
        },
        selectedProdi: function (newProdi) {
            this.setRole();
        }
    },
    methods: {
        isLoading() {

        },
        fetchFakultas() {
            axios.get("http://127.0.0.1:8000/api/fakultas/")
                .then(response => {
                    this.Fakultas = response.data.map(item => item.Nama_fakultas);
                })
                .catch(error => {
                    console.error("Error gagal mengambil data fakultas", error);
                });
        },
        fetchProdi() {
            axios.get("http://127.0.0.1:8000/api/prodi/")
                .then(response => {
                    this.Prodi = response.data.map(item => item.Nama_prodi);
                })
                .catch(error => {
                    console.error("Error gagal mengambil data prodi", error);
                });
        },
        fetchInstansi() {
            axios.get("http://127.0.0.1:8000/api/instansi/")
                .then(response => {
                    this.Instansi = response.data.map(item => item.Nama_instansi);
                })
                .catch(error => {
                    console.error("Error gagal mengambil data instansi", error);
                });
        },
        async addUser() {
            this.loading = true;
            if (this.password !== this.konfpass) {
                this.konfpassErrorMessages = ['Password tidak sesuai'];
                this.loading = false;
            }
            else {
                const userData = {
                    NIM_NIDN: this.NIM_NIDN,
                    email: this.email,
                    password: this.password,
                    user_role: this.user_role,
                    user_priority: this.user_priority
                };

                axios.get(`http://127.0.0.1:8000/api/prodi/getIdByName/${this.selectedProdi}`)
                    .then(response => {
                        this.selectedProdiID = response.data.ProdiID;
                        axios.get(`http://127.0.0.1:8000/api/instansi/getIdByName/${this.selectedInstansi}`)
                            .then(response => {
                                this.selectedInstansiID = response.data.InstansiID;

                                const peminjamData = {
                                    name: this.name,
                                    selectedInstansiID: this.selectedInstansiID,
                                    selectedProdiID: this.selectedProdiID
                                };

                                axios.post("http://127.0.0.1:8000/api/user/", userData)
                                    .then(({ data }) => {
                                        console.log(data);
                                        try {
                                            const UserID = data.UserID;
                                            const peminjamDataWithUserID = { ...peminjamData, UserID: UserID };
                                            axios.post("http://127.0.0.1:8000/api/peminjam/", peminjamDataWithUserID)
                                                .then(peminjamResponse => {
                                                    this.$router.push({ name: 'afterRegistration' });
                                                })
                                                .catch(peminjamError => {
                                                    console.error("Data tidak berhasil dimasukkan ke tabel Peminjam", peminjamError);
                                                });
                                        } catch (err) {
                                            alert("gagal");
                                        } finally {
                                            this.loading = false;
                                        }
                                    })
                                    .catch(userError => {
                                        console.error("Data tidak berhasil dimasukkan ke tabel User", userError);
                                        this.loading = false;
                                    });
                            })
                            .catch(error => {
                                console.error('Error fetching InstansiID:', error);
                                this.loading = false;
                            });
                    })
                    .catch(error => {
                        console.error('Error fetching ProdiID:', error);
                        this.loading = false;
                    });
            }
        },
        setRole() {
            if (!this.email) return '';

            const domain = this.email.split('@')[1];
            const nimPrefix = this.NIM_NIDN.slice(0, 2);
            const selInstansi = this.selectedInstansi;
            const selFakultas = this.selectedFakultas;
            const selProdi = this.selectedProdi;
            if ((domain === 'ti.ukdw.ac.id' || domain === 'si.ukdw.ac.id') && (nimPrefix === '71' || nimPrefix === '72')) {
                return this.user_role = 'Mahasiswa',
                    this.user_priority = 1;
            } else if ((domain === 'ti.ukdw.ac.id' || domain === 'si.ukdw.ac.id') && (nimPrefix !== '71' || nimPrefix !== '72')) {
                return this.user_role = 'Dosen',
                    this.user_priority = 2;
            } else if ((domain === 'students.ukdw.ac.id')) {
                return this.user_role = 'Mahasiswa',
                    this.user_priority = 1;
            } else if ((domain === 'staff.ukdw.ac.id') && (selInstansi) && (selFakultas === 'Lainnya')) {
                return this.user_role = 'Staff',
                    this.user_priority = 2;
            } else if ((domain === 'staff.ukdw.ac.id') && (selFakultas) && (selProdi) && (selInstansi === 'Lainnya')) {
                return this.user_role = 'Dosen',
                    this.user_priority = 2;
            } else {
                return 'Silahkan gunakan email UKDW.'
            }
        },
        fetchProdiByFakultas() {
            axios.get(`http://127.0.0.1:8000/api/fakultas/getIdByName/${this.selectedFakultas}`)
                .then(response => {
                    this.selectedFakultasID = response.data.FakultasID;
                    axios.get(`http://127.0.0.1:8000/api/prodi/getNameByFakultas/${this.selectedFakultasID}`)
                        .then(prodiResponse => {
                            this.Prodi = prodiResponse.data;
                        })
                        .catch(error => {
                            console.error('Error fetching Nama-nama Prodi', error);
                        })
                })
                .catch(error => {
                    console.error('Error fetching FakultasID:', error);
                });
        },
    }
}
</script>

<style lang="scss" scoped></style>