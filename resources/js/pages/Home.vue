<template>
    <navbar-component />
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h4 class="mt-3">Welcome {{ user.name }}</h4>
                <div class="card mt-3">
                    <div class="card-header">
                        <h4>TASKS</h4>
                        <router-link to="/add-task" class="btn btn-primary">ADD TASK</router-link>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="allTasks">
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Title</label>
                                        <input type="text" name="title" v-model="taskFilter.title" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label for="" class="form-label">Description</label>
                                        <input type="text" name="title" v-model="taskFilter.description" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Date Completed (From, To)</label>
                                        <input type="date" name="completed_date_from" v-model="taskFilter.completed_date_from" class="form-control mb-2">
                                        <input type="date" name="completed_date_to" v-model="taskFilter.completed_date_to" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Due Date (From, To)</label>
                                        <input type="date" name="due_date_from" v-model="taskFilter.due_date_from" class="form-control mb-2">
                                        <input type="date" name="due_date_to" v-model="taskFilter.due_date_to" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Archive Date (From, To)</label>
                                        <input type="date" name="archive_date_from" v-model="taskFilter.archive_date_from" class="form-control mb-2">
                                        <input type="date" name="archive_date_to" v-model="taskFilter.archive_date_to" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="mb-2">
                                        <label for="" class="form-label">Sort</label>
                                        <select name="sort" v-model="taskFilter.sort" class="form-control mb-2">
                                            <option value="title" selected>Title</option>
                                            <option value="description">Description</option>
                                            <option value="due_date">Due Date</option>
                                            <option value="created_at">Created At</option>
                                            <option value="date_completed">Date Completed</option>
                                            <option value="priority">Priority</option>
                                        </select>

                                        <select name="sort_level" v-model="taskFilter.sort_level" class="form-control">
                                            <option value="asc" selected>Priority: Low to Urgent</option>
                                            <option value="desc">Priority: Urgent to Low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered table-dark table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Title</th>
                                        <th>Due Date</th>
                                        <th>Tags</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Date Completed</th>
                                        <th>Date Created</th>
                                        <th>Archive Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="task in tasks.data" :key="task.id">
                                        <td>{{ task.id }}</td>
                                        <td>{{ task.user.name }}</td>
                                        <td><router-link :to="'/view-task/'+task.id">{{ task.title }}</router-link></td>
                                        <td>{{ task.due_date }}</td>
                                        <td>
                                            <span v-for="tag in task.tags" :key="tag.id">
                                                {{ tag.text }},
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="task.priority.priority_num == 1" class="badge bg-success">{{ task.priority.text }}</span>
                                            <span v-if="task.priority.priority_num == 2" class="badge bg-primary">{{ task.priority.text }}</span>
                                            <span v-if="task.priority.priority_num == 3" class="badge bg-warning">{{ task.priority.text }}</span>
                                            <span v-if="task.priority.priority_num == 4" class="badge bg-danger">{{ task.priority.text }}</span>
                                        </td>
                                        <td>{{ task.status }}</td>
                                        <td>{{ task.date_completed }}</td>
                                        <td>{{ task.created_at }}</td>
                                        <td>{{ task.archive }}</td>
                                        <td class="d-grid" v-if="!task.archive_date">
                                            <router-link :to="'/edit-task/'+task.id" class="btn btn-primary btn-sm mb-1">Edit</router-link>
                                            <button @click="deleteTask(task.id)" class="btn btn-danger btn-sm">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-center">
                            <pagination align="center" :data="tasks" :limit="this.total" @pagination-change-page="allTasks">
                                <template #prev-nav>
                                    <span>Previous</span>
                                </template>
                                <template #next-nav>
                                    <span>Next</span>
                                </template>
                            </pagination>
                        </div>
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
            user: {
                name: "",
            },
            tasks: {},
            taskFilter: {},
            errMsg: "",
            access_token: localStorage.getItem("access_token"),

            links: [],
            total: 0,
            pages: [],
        };
    },

    created() {
        this.getUser();
        this.allTasks();
    },

    methods: {
        getUser() {
            axios.get("/api/user", {
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                },
            }).then((response) => {
                this.user.name = response.data.data.name;
            }).catch((error) => {
                this.errMsg = error.response.data.message;
            });
        },

        allTasks(page = 1) {
            axios.get('/api/task?page='+page,{
                params: this.taskFilter,
                headers: {
                    'Authorization': 'Bearer ' + this.access_token
                }
            })
            .then((response) => {
                this.tasks = response.data;
                this.total = response.data.meta.total;
            }).catch((error) => {
                console.log(error)
            });
        },

        deleteTask(id) {
            axios.delete(`/api/task/${id}`, {
                headers: {
                    Authorization: `Bearer ${this.access_token}`
                }
            }).then((response) => {
                console.log(response);
                this.allTasks();
            }).catch((error) => {
                console.log(error);
            })
        },
    },
};
</script>

<style>
    .sr-only{
        display: none;
    }
</style>
