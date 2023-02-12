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
                            <li v-for="list in listFolder" :class="[ idActive == list.id ? 'active' : '' ]" :key="list.id" @click="activeFolder(list.id)">{{ list.name }}</li>
                        </ul>
                    </div>
                    <div class="col-md-9 col-lg-9">
                        <p>Số dòng còn lại: 1000</p>
                        <p>Xin hãy tải lên file <strong style="color: red">txt </strong></p>
                        <form class="d-flex " action="" method="post" enctype="multipart/form-data" id="form-upload">
                            <input type="file" class="form-control w-auto" name="file" id="file" accept=".txt">
                            <button type="submit" id="btn-submit" class="btn btn-success mx-1">Tải lên</button>
                        </form>
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
            idActive: 0,
            isOpenAddNew: false,
            nameFolder: '',
            errorNameFolder: '',
            messageSuccess: '',
            listFolder: [
                {
                    id: 1,
                    name: 'test1',
                    api: 'test1',
                    numberText: 1000,
                },
                {
                    id: 2,
                    name: 'test1',
                    api: 'test1',
                    numberText: 1000,
                },
                {
                    id: 3,
                    name: 'test1',
                    api: 'test1',
                    numberText: 1000,
                }

            ]
        }
    },
    methods : {
        activeFolder(idFolder) {
            this.idActive = idFolder
            const folder = this.listFolder.find(list => list.id = idFolder)
            if(folder) {
                axios.get(`/api/get-row-name?name=${folder.api}`).then(response => {
                    console.log(response)
                }).catch(error => {
                    console.log(error)
                })

            }
            // console.log(idFolder)
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
                    console.log(response)
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
                this.idActive = this.listFolder[0].id
            }).catch(error => {
                console.log(error)
            })
        },
        openAddNew() {
            this.isOpenAddNew = !this.isOpenAddNew
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
