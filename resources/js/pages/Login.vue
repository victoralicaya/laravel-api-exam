<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 mt-5">
                <div class="alert alert-danger" v-if="errMsg.message != null">
                    {{ errMsg.message }}
                </div>
                <div class="card mt-3">
                    <div class="card-header text-center">TODO APP</div>
                    <div class="card-body">
                        <form @submit.prevent="loginCheck">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" v-model="login.email" @keydown="() => { errMsg.email = ''; }" />
                                <span class="text-danger">{{ errMsg.email }}</span>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" v-model="login.password" @keydown="() => { errMsg.password = ''; }"  />
                                <span class="text-danger">{{ errMsg.password }}</span>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block">LOGIN</button>
                                <br />
                                <router-link to="/register" class="btn btn-info btn-block text-white">REGISTER</router-link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            login: {},
            errMsg: {},
        };
    },

    methods: {
        loginCheck() {
            axios.post("/api/login", this.login)
                .then((response) => {
                    localStorage.setItem("access_token", response.data.token);
                    this.$router.push("/home");
                }).catch((error) => {
                    let err = error.response.data;
                    if (error.response.status === 422) {
                        if (err.errors.email) {
                            this.errMsg.email = err.errors.email[0];
                        }
                        if (err.errors.password) {
                            this.errMsg.password = err.errors.password[0];
                        }
                    }

                    if (error.response.status === 404) {
                        this.errMsg.message = err.message;
                    }
                });
        },
    },
};
</script>
