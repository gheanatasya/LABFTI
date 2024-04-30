<template>
    <div style="height: 100%; width: 100%; display: flex;">
        <v-container style="height: 100%; width: 38%; float: left; margin: 0; padding: 0;">
            <v-img src="../picture/regis-login.jpeg"></v-img>
        </v-container>

        <v-container style="height: 100%; width: 72%; float: clear;">
            <div style="display: flex; flex-direction: column;">
                <router-link to="/"><v-icon style="font-size: 30px;">mdi-keyboard-backspace</v-icon></router-link>
                <div style="font-family:Lexend-Medium; margin-left:250px; margin-top: 150px; font-size: 25px;">Selamat
                    datang di LAB FTI UKDW!</div>
                <v-form @submit.prevent="login" method="post">
                    <v-sheet
                        style="font-family:Lexend-Regular; margin-left: 200px; margin-right: 200px; margin-top: 30px;"
                        height="200px" class="px-4 py-3">
                        <v-text-field variant="outlined" v-model="formData.email" label="Email" type="email"
                            required></v-text-field>
                        <p class="text-danger" v-text="errors.email"></p>
                        <v-text-field variant="outlined" v-model="formData.password" label="Password" type="password"
                            required></v-text-field>
                        <p class="text-danger" v-text="errors.password"></p>
                    </v-sheet>

                    <v-btn :loading="loading" type="submit" @click="login"
                        style="margin-top: 0px; margin-left: 370px; font-family: Lexend-Medium; background-color: rgb(2, 39, 10, 0.9); color: white; width: 200px; border-radius: 20px; font-size: 17px;">
                        <span v-if="!loading">Login</span>
                        <span v-else>Loading...</span>
                    </v-btn>
                </v-form>
            </div>

            <div>
                <p style="margin-left: 220px; margin-top: 80px; font-family:Lexend-Regular;">Belum memiliki akun?
                    <router-link style="color: rgb(2, 39, 10, 0.9); font-family: Lexend-Bold;"
                        to="/registrationPage">Daftar</router-link>
                </p>
            </div>
        </v-container>
    </div>
</template>

<script>
import axios from 'axios'

export default {
    name: "loginPage",
    data() {
        return {
            formData: {
                email: "",
                password: "",
            },
            loading: false,
            errors: {}
        }
    },
    methods: {
        /*  login() {  */
        /*  this.loading = true; */
        /*  axios.post('api/login', this.formData).then((response) => { */
        /*      localStorage.setItem('token', response.data) */
        /*      this.$router.push('beranda') */
        /*  }).catch((errors) => { */
        /*      this.errors = errors.response.data.errors */
        /*      this.loading = false */
        /*  }) } */
        async login() {
            try {
                this.loading = true;
                const response = await axios.post("http://127.0.0.1:8000/api/login", this.formData);
                if (response.data.token) {
                    localStorage.setItem('token', response.data.token);
                    this.$route.commit('LOGIN');
                    this.$route.push('/beranda')
                }
            } catch (error) {
                console.error("Error login")
                this.loading = false;
            }
        }
    }
};
</script>

<style scoped></style>