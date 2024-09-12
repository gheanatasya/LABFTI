<template>
    <v-dialog v-model="dialogReset" max-width="600" persistent>
        <v-card style="border-radius: 20px; font-family: 'Lexend-Regular'; padding: 10px; width: 600px; height: 250px;">
            <v-card-title style="font-family: 'Lexend-Medium'; text-align: center;">
                Reset Password</v-card-title>

            <v-card-text style="text-align: center;">
                <v-text-field type="email" label="Email" v-model="this.email" clearable
                    variant="outlined"
                    style="color: black; height: 56px; margin-bottom: 25px;"></v-text-field>
            </v-card-text>

            <v-card-actions style="justify-content:center;">
                <v-btn style="position: absolute; top: 0; left: 0; margin-top: 17px;"
                    @click="navigateBackLogin()"><v-icon style="font-size: 30px;">mdi-arrow-left</v-icon></v-btn>
                <v-btn :loading="this.loading"
                    @click="resetPasswordLinkSend(this.email)"
                    style="bottom: 18px; right: 0px; background-color: #0D47A1; color: white;
            border-radius: 5px; width: 300px;">Kirim link reset password</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
import axios from 'axios'

export default {
    name: "resetPassword",
    data() {
        return {
            email: '',
            dialogReset: true,
            loading: false
        }
    },
    methods: {
        navigateBackLogin() {
            this.loading = false
            this.$router.push({ name: 'loginPage' })
        },
        resetPasswordLinkSend(email) {
            this.loading = true
            axios.post('http://127.0.0.1:8000/api/password-reset-link/', { email: email }).then((response) => {
                console.log(response.data);
                this.loading = false
                this.dialogReset = false
                this.$router.push({ name: 'loginPage' })
            }).catch((error) => {
                this.loading = false
                alert('Email tidak terdaftar')
                console.log(error);
            })
        }
    }
}
</script>