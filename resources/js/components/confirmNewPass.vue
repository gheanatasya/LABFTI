<template>
    <v-dialog v-model="dialogReset" max-width="600" persistent>
        <v-card style="border-radius: 20px; font-family:Arial, Helvetica, sans-serif ; padding: 10px; width: 600px; height: 400px;">
            <v-card-title style="font-family:Arial, Helvetica, sans-serif; text-align: center;">
                Reset Password</v-card-title>

            <v-card-text style="text-align: center;">
                <v-text-field type="email" label="Email" v-model="this.email" clearable variant="outlined"
                    style="color: black; height: 56px; margin-bottom: 25px; margin-right: 40px;"></v-text-field>
                <v-text-field variant="outlined" v-model="this.password" label="Password"
                    style="height: 80px;" :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="show ? 'text' : 'password'" @click:append="show = !show"></v-text-field>
                <v-text-field variant="outlined" v-model="this.konfpass" label="Konfirmasi Password"
                    :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'" :type="show1 ? 'text' : 'password'"
                    @click:append="show1 = !show1"
                    style="height: 80px;"></v-text-field>
            </v-card-text>

            <v-card-actions style="justify-content:center;">
                <v-btn :loading="this.loading" @click="resetPassword(this.email, this.password, this.konfpass)" style="bottom: 20px; right: 0px; background-color: rgb(2,39, 10, 0.9); color: white;
            border-radius: 5px; width: 200px;">Reset Password</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios'

export default {
    name: "confirmNewPass",
    data() {
        return {
            email: '',
            password: '',
            konfpass: '',
            dialogReset: true,
            loading: false,
            show: true,
            show1: true
        }
    },
    methods: {
        resetPassword(email, password, konfpass) {
            this.loading = true

            if (email == '' || password == '' || konfpass == '') {
                this.loading = false
                alert('Terdapat field yang kosong!')
                return
            } else if (password != konfpass) {
                this.loading = false
                alert('Konfirmasi password tidak sesuai')
                return
            }

            axios.post('http://127.0.0.1:8000/api/resetPassword', { email: email, password: password, konfpass: konfpass, token: this.$route.params.token }).then((response) => {
                if (response.data.success) {
                    console.log(response.data);
                    this.loading = false
                    this.$router.push({ name: 'loginPage' })
                } else {
                    this.loading = false
                    alert(response.data.message);
                    this.email = ''
                    this.password = ''
                    this.konfpass = ''
                }
            }).catch((error) => {
                this.loading = false
                alert('Gagal reset password.')
                console.log(error);
            })
        }
    }
}
</script>