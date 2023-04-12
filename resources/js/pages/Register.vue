<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 mt-5">
                <div class="card mt-3">
                    <div class="card-header text-center">REGISTER</div>

                    <div class="card-body">
                        <form @submit.prevent="register">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" v-model="user.name" @keydown="() => { errMsg.name = ''; }" />
                                <span class="text-danger">{{ errMsg.name }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" v-model="user.email" @keydown="() => { errMsg.email = ''; }" />
                                <span class="text-danger">{{ errMsg.email }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" v-model="user.password" @keydown="() => { errMsg.password = ''; }" />
                                <span class="text-danger">{{ errMsg.password }}</span>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-block mb-3">REGISTER</button>
                                <router-link to="/" class="btn btn-info btn-block text-white">LOGIN</router-link>
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
            user: {},
            errMsg: {},
        };
    },

    methods: {
        register() {
            axios.post("/api/registration", this.user)
                .then((response) => {
                    localStorage.setItem("access_token", response.data.token);
                    this.$router.push('/home');
                }).catch((error) => {
                    let err = error.response.data;
                    if (err.errors.name) {
                        this.errMsg.name = err.errors.name[0];
                    }

                    if (err.errors.email) {
                        this.errMsg.email = err.errors.email[0];
                    }

                    if (err.errors.password) {
                        this.errMsg.password = err.errors.password[0];
                    }
                });
        },
    },
};
</script>
