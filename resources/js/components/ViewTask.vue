<template>
    <navbar-component />
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="mt-3">
                    <div class="d-flex ">
                        <router-link to="/home" class="btn btn-info text-white me-1">Back</router-link>
                        <div v-if="!task.archive">
                            <button v-if="task.status == 'incomplete'" class="btn btn-primary me-1" @click="markAsCompleteIncomplete">Mark as Complete</button>
                            <button v-else class="btn btn-danger me-1" @click="markAsCompleteIncomplete">Mark as Incomplete</button>
                        </div>
                        <button v-if="!task.archive" class="btn btn-warning text-white" @click="archiveTask">Archive Task</button>
                        <button v-if="task.archive" class="btn btn-success" @click="archiveTask">Recover Task</button>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h4>{{ task.title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div v-if="task.status == 'completed'" class="badge bg-success w-100">
                                    <b>Date Completed: {{ task.date_completed }}</b>
                                </div>
                                <div v-if="task.due_date != null" class="badge bg-danger w-100">
                                    <b>Due Date: {{ task.due_date }}</b>
                                </div>
                                <div v-if="task.archive" class="badge bg-danger w-100">
                                    <b>Date Archived: {{ task.archive }}</b>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p>{{ task.description }}</p>
                        <div class="row justify-content-">
                            <div class="col-md-6">
                                <label for="files" class="form-label mt-2"><b>Files</b></label>
                                <ul class="list-group">
                                    <li v-for="uploadedFile in uploadedFiles" :key="uploadedFile.id" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" class="nav-link">{{ uploadedFile.name }}</a>
                                            <button v-if="!task.archive && !task.date_completed" @click="deleteFile(uploadedFile.id)" class="btn btn-danger btn-sm">Delete</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <label for="files" class="form-label mt-2"><b>Tags</b></label>
                                <ul class="list-group">
                                    <li v-for="addedTag in addedTags" :key="addedTag.id" class="list-group-item list-group-item-action">
                                        <div class="d-flex justify-content-between">
                                            <a href="#" class="nav-link">{{ addedTag.text }}</a>
                                            <button v-if="!task.archive && !task.date_completed" class="btn btn-danger btn-sm" @click="deleteTag(addedTag.id)">Delete</button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer" v-if="!task.archive && !task.date_completed">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <form @submit.prevent="addFiles" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="formFile" class="form-label">Upload File/s</label>
                                        <input type="file" name="attachments" multiple id="formFile" ref="attachments" class="form-control" @change="fileUpload">
                                        <span class="text-danger">{{ errMsg }}</span>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <form @submit.prevent="addTags">
                                    <label for="tags" class="form-label">Tags</label>
                                    <div class="input-group mb-3" v-for="(tag, index) in tags" :key="index">
                                        <input type="text" class="form-control" v-model="tag.text" :id="index" required>
                                        <button class="btn btn-primary" v-if="index == 0" @click="addTagField">+</button>
                                        <button class="btn btn-danger" v-if="index != 0" @click="removeTagField(index, $event)">x</button>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a :href="'/view-task/'+task.id" class="btn btn-danger">Cancel</a>
                                    </div>
                                </form>
                            </div>
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
            task: {},
            attachments: [],
            uploadedFiles: [],
            tags: [{
                text: '',
            }],
            addedTags: [],
            formData: new FormData,
            errMsg: '',
            access_token: localStorage.getItem("access_token"),
            buttonText: '',
        }
    },

    created() {
        this.getTaskData();
    },

    methods: {
        fileUpload(event) {
            let selectedFiles = event.target.files;
            this.errMsg = '';
            for (let i = 0; i < selectedFiles.length; i++) {
                this.attachments.push(selectedFiles[i]);
            }
        },

        getTaskData() {
            axios.get(`/api/task/${this.$route.params.id}`, {
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                }
            }).then((response) => {
                let data = response.data.data;
                this.task.id = data.id;
                this.task.title = data.title;
                this.task.description = data.description;
                this.task.due_date = data.due_date;
                this.task.priority = data.priority.text;
                this.task.tags = data.tags;
                this.task.status = data.status;
                this.task.date_completed = data.date_completed;
                this.task.archive = data.archive_date;
                this.uploadedFiles = data.files;
                this.addedTags = data.tags;
            }).catch((error) => {
                let errStatus = error.response.status
                if (errStatus === 404 || errStatus === 401) {
                    this.$router.push('/home');
                }
            })
        },

        addFiles() {
            for (let i = 0; i < this.attachments.length; i++) {
                this.formData.append('files[]', this.attachments[i]);
                this.formData.append('task_id', this.$route.params.id);
            }
            axios.post('/api/file-upload', this.formData,{
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                    'Content-Type': 'multipart/form-data',
                }
            }).then((response) => {
                window.location.reload();
            }).catch((error) => {
                this.errMsg = error.response.data.errors.files[0];
            });
        },

        deleteFile(id) {
            axios.delete(`/api/file-upload/${id}`, {
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                }
            }).then((response) => {
                    this.getTaskData();
                }).catch((error) => {
                    console.log(error);
                })
        },

        addTags() {
            for (let i = 0; i < this.tags.length; i++) {
                this.formData.append('text[]', this.tags[i].text);
                this.formData.append('task_id', this.$route.params.id);
            }

            axios.post('/api/tag', this.formData, {
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                }
            }).then((response) => {
                this.getTaskData();
                this.tags.splice(1, this.tags.length);
                for (let index = 0; index < this.tags.length; index++) {
                    this.tags[index].text = '';
                }
                this.formData.delete('text[]');
                this.formData.delete('task_id');
            }).catch((error) => {
                console.log(error.response);
            });
        },

        deleteTag(id) {
            axios.delete(`/api/tag/${id}`, {
                headers: {
                    Authorization: `Bearer ${this.access_token}`,
                }
            }).then((response) => {
                this.getTaskData();
            }).catch((error) => {
                console.log(error.response);
            })
        },

        markAsCompleteIncomplete() {
            fetch('/api/changeStatus/'+this.$route.params.id, {
                method: 'PUT',
                headers: {
                    Authorization: 'Bearer ' + this.access_token,
                    Accept: 'applcation/json'
                }
            }).then((response) => {
                response.json();
                this.getTaskData();
            }).catch((error) => {
                console.log(error);
            })
        },

        archiveTask() {
            fetch('/api/archive/'+this.$route.params.id, {
                method: 'PUT',
                headers: {
                    Authorization: 'Bearer ' + this.access_token,
                    Accept: 'application/json'
                }
            }).then((response) => {
                response.json();
                this.getTaskData();
            }).catch((error) => {
                console.log(error);
            })
        },

        addTagField(e) {
            e.preventDefault();
            this.tags.push({value: ''});
        },

        removeTagField(index, e) {
            e.preventDefault();
            this.tags.splice(index, 1);
            this.tags
        }
    },
}
</script>
