<template>
    <div class="justify-content-end d-flex">
        <button class="btn btn-primary" @click="openAddNew">Thêm mới danh sách</button>
    </div>
    <div class="alert alert-success my-2 alert-dismissible fade show" role="alert" v-if="messageSuccess">
        {{ messageSuccess }}
        <button type="button" class="btn-close" @click="messageSuccess = ''" aria-label="Close"></button>
    </div>
    <div class="card my-2" v-if="isOpenAddNew">
        <div class="card-header">
            Thêm mới 1 danh sách
        </div>
        <div class="card-body">
            <div class="d-flex">
                <label class="d-block align-self-center">Tên Danh sách:</label>
                <input type="text" class="form-control mx-1" v-model="nameFolder" style="width: auto">
                <button class="btn btn-success mx-1" @click="addNewFolder">Thêm mới</button>
            </div>
            <span style="color: red" class="my-2 d-block">{{ errorNameFolder }}</span>
        </div>
    </div>
    <div class="my-3">
        <div class="card">
            <div class="card-header">
                Tổng quan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-lg-3 border-end">
                        <ul class="px-0">
                            <li v-for="list in listFolder" v-bind:class="{'active': idActive === list.id}" :key="list.id" @click="activeFolder(list.id)">{{ list.name }}</li>
                        </ul>
                    </div>
                    <div class="col-md-9 col-lg-9">
                        <div>Số dòng còn lại:
                            <span style="color: green" v-if="textCount !== null">
                                {{ textCount }}
                            </span>
                            <div v-else class="spinner-border text-primary" role="status" style="width: 20px; height: 20px">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <p>Xin hãy tải lên file <strong style="color: red">txt </strong></p>
                        <form class="d-flex " action="" method="post" enctype="multipart/form-data" id="form-upload">
                            <input type="file" @change="changeFile" class="form-control w-auto" name="file" id="file" accept=".txt">
                            <button type="button" id="btn-submit" v-if="!isLoading" @click="upload" class="btn btn-success mx-1">Tải lên</button>
                            <div v-else class="spinner-border text-primary" role="status" style="width: 20px; height: 20px">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </form>
                        <span style="color: red" v-if="errorMessageFile" class="d-block my-2">{{ errorMessageFile }}</span>
                        <span style="color: green" v-if="successMessageFile" class="d-block my-2">{{ successMessageFile }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
export default {
    name: "App",
    mounted() {
        this.getFolder()
    },
    data() {
        return {
            idActive: null,
            isOpenAddNew: false,
            nameFolder: '',
            isLoading: false,
            errorNameFolder: '',
            messageSuccess: '',
            listFolder: [],
            errorMessageFile: '',
            successMessageFile: '',
            file: {},
            textCount: null
        }
    },
    methods : {
        activeFolder(idFolder) {
            this.idActive = idFolder
            this.textCount = null
            const folder = this.listFolder.find(list => list.id === idFolder)
            if(folder) {
                axios.get(`/api/get-row/${idFolder}`).then(response => {
                    this.textCount = response.data.data.text_count ?? 0
                }).catch(error => {
                    console.log(error)
                })

            }
        },
        addNewFolder() {
            if(!this.nameFolder) {
                this.errorNameFolder = 'Vui lòng nhập tên danh sách'
                return
            }else if(this.nameFolder.length > 255) {
                this.errorNameFolder = 'Tên danh sách không vượt quá 255 ký tự'
                return
            } else {
                axios.post(`/api/add-folder`, {
                    name: this.nameFolder
                },).then(response => {
                    this.idActive = response.data.data.folder_id
                    this.messageSuccess = 'Thêm mới danh sách thành công'
                    this.nameFolder = ''
                    this.errorNameFolder = ''
                    this.getFolder()
                }).catch(error => {
                    this.errorNameFolder = error.response.data.message
                })
            }
        },
        getFolder() {
            axios.get(`/api/get-folder`).then(response => {
                this.listFolder = response.data.data
                this.idActive = !this.idActive ? this.listFolder[0].id : this.idActive
                this.activeFolder(this.idActive)
            }).catch(error => {
                console.log(error)
            })
        },
        openAddNew() {
            this.isOpenAddNew = !this.isOpenAddNew
        },
        changeFile(event) {
            if (!event || !event.target || !event.target.files || event.target.files.length === 0) {
                return;
            }
            const file = event.target.files[0]
            const name = file.name;
            const lastDot = name.lastIndexOf('.');
            const fileName = name.substring(0, lastDot);
            const ext = name.substring(lastDot + 1);
            if(ext !== 'txt') {
                this.errorMessageFile = `File ${name} sai định dạng txt`
                return;
            }else {
                this.errorMessageFile = ''
            }
            const fileSize = Math.floor((file.size / 1024 / 1024) * 100) / 100
            if(fileSize > 2000) {
                this.errorMessageFile = `File ${name} đã quá tải, yêu cầu < hoặc = 2gb`
                return;
            }
            this.file = file

        },
        upload() {
            this.isLoading = true
            let formData = new FormData()
            formData.append('file', this.file)
            formData.append('folder_id', this.idActive)
            axios.post(`/api/upload`, formData).then(response => {
                this.successMessageFile = response.data.message
                this.errorMessageFile = ''
                this.isLoading = false
            }).catch(error => {
                this.errorMessageFile = error.response.data.message
                this.isLoading = false
            })
        }
    }
}
</script>

<style scoped>
ul li {
    list-style: none;
    margin-top: 10px;
    margin-bottom: 10px;
    border-radius: 10px;
    padding: 5px;
}
ul li:hover {
    background: #83ad83;
    color: #ffffff;
    cursor: pointer;
    border-radius: 10px;
}
ul li.active {
    background: green;
    color: #ffffff;
}
</style>
