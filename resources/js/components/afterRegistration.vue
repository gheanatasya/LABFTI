<template>
    <div style="height: 100%; width: 100%; display: flex;">
        <v-container style="width: 45%; margin: 0; padding: 0;">
            <v-img src="../picture/regis-login.jpeg" style="height: 100%;"></v-img>
        </v-container>

        <v-container style="height: 100%; width: 72%;">
            <div style="margin-top: 260px; margin-left: 150px;">
                <p style="font-family: 'Lexend-Bold'; font-size: 25px;">Registrasi berhasil! <br>
                    Silahkan masuk menggunakan email dan password <br> yang telah di daftarkan. 
                    <router-link style="color: #0D47A1; font-family: Lexend-Medium"
                        to="/loginPage"><v-icon>mdi-arrow-right-thin</v-icon>Selanjutnya</router-link>
                </p>
            </div>
        </v-container>
    </div>
</template>

<script>

export default {
    name: "afterRegistration",
    data() {
        return {
            
        }
    },
    mounted() {
        
    },
    methods: {

            }
}
</script>

<style lang="scss" scoped></style>