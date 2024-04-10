<template>
    <headerUser></headerUser>

    <div>
        <p style="font-family: Lexend-Medium; font-size: 28px; margin-left: 40px; margin-top: 20px;">Profil</p>

        <div> 
            <v-container>
                <v-icon style="font-size: 200px;">mdi-account-circle</v-icon>
                <v-icon style="font-size:30px;">mdi-pencil</v-icon>

                <p style="font-family: Lexend-Regular; font-size: 20px;"> {{ akun.nama }} </p>
                <p style="font-family: Lexend-Regular; font-size: 20px;"> {{ akun.nim_nidn }}</p>
                <p style="font-family: Lexend-Regular; font-size: 20px;"> {{ akun.role }}</p>

            </v-container>

            <v-container>

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
        data(){
            return {

            }
        },
        methods: {
            getUserData(){
                axios
            }
        }
        
    }
</script>

<style lang="scss" scoped>

</style>