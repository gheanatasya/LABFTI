<template>
    <headerUser style="z-index: 1"></headerUser>

    <div style="margin-top: 70px;">
        <p style="font-family: Lexend-Medium; font-size: 28px; margin-left: 40px; margin-top: 20px;">Profil</p>

        <div style="width: 100%; display: flex;">
            <v-container style="width:40%; margin-left: 300px;">
                <v-icon style="font-size: 250px;">mdi-account-circle</v-icon>
                <v-icon style="font-size:30px;margin-top: 180px; margin-left: -50px;">mdi-pencil</v-icon>

                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.peminjam.Nama }} </p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.NIM_NIDN }}</p>
                <p style="font-family: Lexend-Regular; font-size: 30px; justify-content: center;"> {{ this.user.User_role }}</p>

            </v-container>

            <v-container style="width:60%;">
                <v-sheet
                    style=" background-color: rgb(3, 138, 33, 0.1); font-family: Lexend-Regular; margin-right: 80px;margin-top: -50px; border-radius: 10px;">
                    <v-text-field label="Nama Lengkap" :model-value="this.peminjam.Nama" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px; padding-top: 30px;"></v-text-field>

                    <div v-if="this.user.User_role === 'Mahasiswa' || 'Petugas'">
                        <v-text-field label="NIM" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <div v-else>
                        <v-text-field label="NIDN" :model-value="this.user.NIM_NIDN" variant="outlined" readonly
                            style="margin-right: 50px; margin-left:40px;"></v-text-field>
                    </div>

                    <v-text-field label="Email" :model-value="this.user.Email" variant="outlined" readonly
                        style="margin-right: 50px; margin-left:40px;"></v-text-field>

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

                    <v-btn @click="editprofil" style="margin-top: 10px; margin-left: 420px; margin-bottom: 50px; font-family: Lexend-Medium; 
                    background-color: rgb(2, 39, 10, 0.9); color: white; width: 150px; border-radius: 20px; font-size: 17px;">Edit</v-btn>

                </v-sheet>
            </v-container>
        </div>
    </div>
</template>

<script>
import headerUser from '../components/headerUser.vue'

export default {
    name: "profil",
    components: {
        headerUser,
    },
    data() {
        return {
            user: [],
            peminjam: [],
            instansi: "",
            prodi: "",
            fakultas: "",
            loading: false,
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
        editprofil(){
            this.loading = true;
            this.$router.push({ name: 'editProfil' })        
        }
    }
}
</script>

<style lang="scss" scoped></style>