<template>
    <headerUser></headerUser>

    <div>
        <p style="font-size: 35px; font-family: Lexend-Medium; text-align: center; margin-top: 20px;">Halo, </p>

        <p style="font-family: Lexend-Regular; margin-left: 40px; margin-top: 30px; font-size: 20px;"> Peminjaman ruangan dan alat yang sedang anda lakukan : </p>

        <div class="tabelPinjam"> 
            <v-table height="300px" fixed-header>
                <thead>
                    <tr>
                        <th class="text-center">
                            No
                        </th>

                        <th class="text-center">
                            Ruangan
                        </th>

                        <th class="text-center">
                            Waktu Penggunaan
                        </th>

                        <th class="text-center">
                            Keterangan
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                        <th class="text-center">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        
                    </tr>
                </tbody>
            </v-table>
        </div>
    </div>
</template>

<script>
import headerUser from '../components/headerUser.vue'

    export default {
        name: 'berandaUser',
        components: {
            headerUser,
        },
        data() {
            return {
                no: '',
                ruangan: [],
                waktu: [],
                keterangan: [],
                status: [

                ],
                action: [
                    {icon: 'mdi-download-circle'},
                    {icon: 'mdi-close-circle'},
                ],
                data: undefined,
            }
        }, 
        methods: {
            getData(){
                axios.get('http://127.0.0.1:8000/api/peminjaman/')
                .then(response => {
                    this.data = response.data;
                    this.d

                })
            }
        }

        
    }
</script>

<style lang="scss" scoped>

</style>