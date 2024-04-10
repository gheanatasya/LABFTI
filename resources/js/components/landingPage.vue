<template>
    <div style="background-color: #34743B;">
        <v-container style="background-color: #10511B; display: flex; justify-content: space-between; width: 30%;">
            <v-img :width="90" :height="70" src="../picture/logo-ukdw.png" ></v-img>
            <v-img :width="20" :height="70" src="../picture/fti-ukdw.png" ></v-img>
        </v-container>  
        
        <a href="halaman_login.html" style="position: absolute; top: 20px; right: 20px; color: white; text-decoration: none;">Login</a>
        <div style="text-align:center; color:white; font-family:Lexend-Medium; font-size: 25px; margin-top: 40px;">
            Untuk melakukan peminjaman, silahkan <router-link to="loginpage" style="color:white">Login</router-link> terlebih dahulu.
        </div>

        <v-container>

        </v-container>
    </div>
</template>

<script>
export default {
    name: "landingPage",

}
</script>

<style lang="scss" scoped></style>