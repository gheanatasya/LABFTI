<template>
    <div style="height: 100%; width: 100%; display: flex;">
        <v-container style="height: 100%; width: 45%; float: left; margin: 0; padding: 0;">
            <v-img src="../picture/regis-login.jpeg" cover></v-img>
        </v-container>

        <v-container style="height: 100%; width: 72%; float: clear;">
            <div style="display: flex; flex-direction: column;">
                <router-link to="/"><v-icon style="font-size: 30px;">mdi-keyboard-backspace</v-icon></router-link>
                <div style="font-family:Lexend-Medium; margin-left:250px; margin-top: 150px; font-size: 25px;">Selamat
                    datang di LAB FTI UKDW!</div>
                <div v-if="loginFailed" class="alert alert-danger"
                    style="font-family: Lexend-Regular; color:red; margin-left: 350px; margin-top: 10px;">Email atau
                    Password Anda salah.</div>
                <v-form @submit.prevent="login" method="post">
                    <v-sheet
                        style="font-family:Lexend-Regular; margin-left: 200px; margin-right: 200px; margin-top: 30px; margin-bottom: -30px;"
                        height="200px" class="px-4 py-3">
                        <v-text-field variant="outlined" v-model="email" label="Email" type="email"
                            required></v-text-field>
                        <v-text-field variant="outlined" v-model="password" label="Password"
                            :append-inner-icon="show ? 'mdi-eye' : 'mdi-eye-off'" :type="show ? 'text' : 'password'"
                            @click:append-inner="show = !show" required></v-text-field>
                    </v-sheet>

                    <div>
                        <router-link style="color: #0D47A1; font-family: Lexend-Regular; margin-left: 220px; margin-top: -100px;"
                        to="/resetPassword">Lupa password?</router-link>
                    </div>

                    <v-btn :loading="loading" @click="login"
                        style="text-transform: none; margin-top: 30px; margin-left: 370px; font-family: Lexend-Medium; background-color: #0D47A1; color: white; width: 200px; border-radius: 20px; font-size: 17px;">
                        <span v-if="!loading">Login</span>
                        <span v-else>Loading...</span>
                    </v-btn>
                </v-form>
            </div>

            <div>
                <p style="margin-left: 220px; margin-top: 80px; font-family:Lexend-Regular;">Belum memiliki akun?
                    <router-link style="color: #0D47A1; font-family: Lexend-Bold;"
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
            loggedIn: localStorage.getItem('loggedIn'),
            token: localStorage.getItem('token'),
            UserID: localStorage.getItem('UserID'),
            User_role: localStorage.getItem('User_role'),
            email: "",
            password: "",
            validation: [],
            loginFailed: null,
            loading: false,
            show: false,
        }
    },
    methods: {
        login() {
            this.loading = true
            if (this.email === "" || this.password === "") {
                alert("Email dan Password harus diisi")
                this.loading = false
                return;
            }

            if (this.email && this.password) {
                axios.get('http://localhost:8000/sanctum/csrf-cookie')
                    .then(response => {
                        console.log(response)

                        axios.post('http://localhost:8000/api/login', {
                            email: this.email,
                            password: this.password
                        }).then(res => {
                            console.log(res)

                            if (res.data.success) {
                                localStorage.setItem("loggedIn", "true")

                                localStorage.setItem("token", res.data.token)

                                localStorage.setItem("UserID", res.data.UserID);

                                localStorage.setItem("User_role", res.data.User_role);

                                localStorage.setItem("Total_batal", res.data.Total_batal);

                                localStorage.setItem("loginTime", Date.now());

                                this.loggedIn = true
                                return this.$router.push({ name: 'berandaUser' })
                            } else {
                                this.loginFailed = true
                                this.loading = false
                            }

                            console.log(res.data)
                        }).catch(error => {
                            console.log(error)
                            this.loading = false
                            this.loginFailed = true
                        })
                    })
            } else {
                this.loading = false
                this.loginFailed = true
            }

            this.validation = []

            if (!this.email) {
                this.validation.email = true
            }

            if (!this.password) {
                this.validation.password = true
            }

        }
    },
    
    mounted() {
        if (this.loggedIn) {
            return this.$router.push({ name: 'berandaUser' })
        }
    }
}
    ;
</script>

<style scoped></style>