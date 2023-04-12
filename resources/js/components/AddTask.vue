<template>
    <navbar-component />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-3">
                    <div class="card-header text-center">
                        TODO APP
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="addTask" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" v-model="task.title" @keydown="() => { errMsg.title = ''; }">
                                <span class="text-danger">{{ errMsg.title }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" cols="20" rows="5" class="form-control" v-model="task.description" style="resize: none;" @keydown="() => { errMsg.description = ''; }"></textarea>
                                <span class="text-danger">{{ errMsg.description }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="duedate" class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control" v-model="task.due_date" @change="() => { errMsg.due_date = ''; }">
                                <span class="text-danger">{{ errMsg.due_date }}</span>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Select Priority</label>
                                <select name="priority" v-model="task.priority" class="form-select">
                                    <option v-for="prios in priorities" :key="prios.code" :value="prios.code">{{ prios.text }}</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                <input type="reset" value="Cancel" class="btn btn-danger" @click="() => { this.$router.push('/home'); }">
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
            task: {
                priority: 1
            },
            priorities: [
                { code: 1, text: 'Low' },
                { code: 2, text: 'Normal' },
                { code: 3, text: 'High' },
                { code: 4, text: 'Urgent' }
            ],
            errMsg: {},
        }
    },

    created() {},

    methods: {
        addTask() {
            let access_token = localStorage.getItem("access_token");
            axios.post('/api/task', this.task, {
                headers: {
                    Authorization: `Bearer ${access_token}`,
                }
            }).then((response) => {
                console.log(response);
                this.task = {};
                this.task.priority = 1;
                this.$router.push('/home');
            }).catch((error) => {
                console.log(error);
                let err = error.response.data;
                if (err.errors.title) {
                    this.errMsg.title = err.errors.title[0];
                }
                if (err.errors.description) {
                    this.errMsg.description = err.errors.description[0];
                }
                if (err.errors.due_date) {
                    this.errMsg.due_date = err.errors.due_date[0];
                }
                if (err.errors.due_date) {
                    this.errMsg.priority = err.errors.priority[0];
                }
            });
        },
    },
}
</script>
