<template>
    <div>
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">
            <div class="container-fluid">
                <router-link to="/" class="navbar-brand">Todo App</router-link>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="#" @click="logout" class="nav-link">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="alert alert-danger mt-2" v-if="errMsg != ''">
            {{ errMsg }}
        </div>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            errMsg: "",
        };
    },

    methods: {
        logout() {
            var access_token = localStorage.getItem("access_token");
            axios.get("/api/logout", {
                    headers: {
                        Authorization: `Bearer ${access_token}`,
                    },
                }).then((response) => {
                    localStorage.removeItem('access_token');
                    this.$router.push("/");
                }).catch((error) => {
                    this.errMsg = error.response.statusText;
                });
        },
    },
};
</script>
