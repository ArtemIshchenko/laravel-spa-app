<template>
    <div class="container">
        <h1>{{name}}</h1>
        <div class="form-group">
            <input type="text" class="form-control" :class="{'is-invalid': $v.name.$error}" v-model.trim="$v.name.$model" @blur="saveName">
            <div class="invalid-feedback" v-if="!$v.name.required">
              Required field
            </div>
            <div class="invalid-feedback" v-if="!$v.name.maxLength">
                Макс. кол. символов: {{$v.name.$params.maxLength.max}}
            </div>
        </div>
        <form @submit.prevent="addNewDeskList">
            <div class="mb-3 mt-3">
                <input type="text" class="form-control" :class="{'is-invalid': $v.desk_list_name.$error}" v-model.trim="$v.desk_list_name.$model" id="deskInputText" placeholder="Enter list name">
                <div class="invalid-feedback" v-if="!$v.desk_list_name.required">
                  Required field
                </div>
                <div class="invalid-feedback" v-if="!$v.desk_list_name.maxLength">
                    Макс. кол. символов: {{$v.desk_list_name.$params.maxLength.max}}
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add list</button>
        </form>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных!
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="(desk_list, index) in desk_lists" :key="desk_list.id">
                <div class="card mt-3">
                    <div class="card-body">
                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="d-flex justify-content-between align-items-center">
                            <input type="text" v-model="desk_list.name" class="form-control" :class="{'is-invalid': $v.desk_lists.$each[index].name.$error}" placeholder="Enter list name">
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.required">
                              Required field
                            </div>
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.maxLength">
                                Макс. кол. символов: {{$v.desk_lists.$each[index].name.$params.maxLength.max}}
                            </div>
                            <button type="button" @click="desk_list_input_id = null" class="btn-close" aria-label="Close"></button>
                        </form>
                        <h4 v-else class="card-title d-flex justify-content-between align-items-center" style="cursor: pointer;" @click="desk_list_input_id = desk_list.id">{{desk_list.name}}<i class="fas fa-pencil-alt" style="font-size: 15px;"></i></h4>
                        <button type="button" class="btn btn-danger mt-3" @click="deleteDeskList(desk_list.id)">Delete list</button>
                        <div class="card mt-3 bg-light" v-for="card in desk_list.cards" :key="card.id">
                            <div class="card-body">
                                <h4 class="card-title d-flex justify-content-between align-items-center mb-3" style="cursor: pointer;">{{card.name}}</h4>
                                <button type="button" @click="getCard(card.id)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Open
                                </button>
                                <button type="button" class="btn btn-secondary" @click="deleteCard(card.id)">Delete</button>
                            </div>
                        </div>
                        <form @submit.prevent="addNewCard(desk_list.id)" class="mt-3">
                            <input type="text" v-model="card_names[desk_list.id]" class="form-control" :class="{'is-invalid': $v.card_names.$each[desk_list.id].$error}" placeholder="Enter card name">
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].required">
                              Required field
                            </div>
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].maxLength">
                                Макс. кол. символов: {{$v.card_names.$each[desk_list.id].$params.maxLength.max}}
                            </div>
                        </form>
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <form @submit.prevent="updateCardName" v-if="show_card_name_input" class="d-flex justify-content-between align-items-center">
                                            <input type="text" v-model="current_card.name" class="form-control" :class="{'is-invalid': $v.current_card.name.$error}" placeholder="Enter card name">
                                            <div class="invalid-feedback" v-if="!$v.current_card.name.required">
                                              Required field
                                            </div>
                                            <div class="invalid-feedback" v-if="!$v.current_card.name.maxLength">
                                                Макс. кол. символов: {{$v.current_card.name.$params.maxLength.max}}
                                            </div>
                                            <button type="button" class="btn-close" aria-label="Close" @click="updateCardName"></button>
                                        </form>
                                        <h5 class="modal-title" style="cursor: pointer" id="exampleModalLabel" v-if="!show_card_name_input" @click="show_card_name_input = true">{{current_card.name}}&nbsp;&nbsp;<i class="fas fa-pencil-alt" style="font-size: 15px;"></i></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check" v-for="(task, index) in current_card.tasks">
                                            <input class="form-check-input" type="checkbox" @change="updateTask(current_card.tasks[index])" :id="'inlineCheckbox1' + index" value="option1" v-model="current_card.tasks[index].is_done">
                                            <form @submit.prevent="updateTask(current_card.tasks[index])" v-if="task_input_name_id == task.id">
                                                <input type="text" v-model="current_card.tasks[index].name" v-if="task_input_name_id == task.id" class="form-control" placeholder="Enter task name">
                                            </form>
                                            <label v-else class="form-check-label" :for="'inlineCheckbox1' + index">{{task.name}}</label>&nbsp;&nbsp;
                                            <span @click="task_input_name_id = task.id" v-if="task_input_name_id != task.id"><i class="fas fa-pencil-alt" style="font-size: 13px;"></i></span>
                                            <button type="button" class="btn-close ml-3" aria-label="Close" @click="deleteTask(task.id)"></button>
                                        </div>
                                        <form @submit.prevent="addNewTask" class="mt-3">
                                            <div class="form-group">
                                                <input type="text" v-model="new_task_name" class="form-control" :class="{'is-invalid': $v.new_task_name.$error}" placeholder="Enter task name">
                                                <div class="invalid-feedback" v-if="!$v.new_task_name.required">
                                                  Required field
                                                </div>
                                                <div class="invalid-feedback" v-if="!$v.new_task_name.maxLength">
                                                    Макс. кол. символов: {{$v.new_task_name.$params.maxLength.max}}
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="spinner-grow" role="status" v-if="loading">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</template>

<script>
    import { required, maxLength } from 'vuelidate/lib/validators'
    export default {
        props: [
            'deskId'
        ],
        data() {
            return {
                name: null,
                desk_list_name: null,
                errored: false,
                loading: true,
                desk_lists: [],
                desk_list_input_id: null,
                card_names: [],
                current_card: [],
                show_card_name_input: false,
                new_task_name: '',
                task_input_name_id: null
            }
        },
        methods: {
            updateTask(task) {
                axios.post('/api/v1/tasks/' + task.id, {
                    _method: 'PATCH',
                    name: task.name,
                    is_done: task.is_done,
                    card_id: task.card_id
                })
                    .then(response => {
                        this.task_input_name_id = null
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            deleteTask(id) {
                axios.post('/api/v1/tasks/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.$v.$reset()
                        this.getCard(this.current_card.id)
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            addNewTask() {
                this.$v.new_task_name.$touch()
                if (this.$v.new_task_name.$anyError) {
                    return
                }
                axios.post('/api/v1/tasks', {
                    name: this.new_task_name,
                    card_id: this.current_card.id
                })
                    .then(response => {
                        this.new_task_name = ''
                        this.$v.$reset()
                        this.getCard(this.current_card.id)
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.errored = true
                    })
            },
            updateCardName() {
                this.$v.current_card.name.$touch()
                if (this.$v.current_card.name.$anyError) {
                    return
                }
                this.show_card_name_input = false
                axios.post('/api/v1/cards/' + this.current_card.id, {
                    _method: 'PATCH',
                    name: this.current_card.name,
                    desk_list_id: this.current_card.desk_list_id
                })
                    .then(response => {
                        this.show_card_name_input = false
                        this.$v.$reset()
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            getCard(id) {
                axios.get('/api/v1/cards/' + id)
                    .then(response => {
                        this.current_card = response.data.data
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            deleteCard(id) {
                axios.post('/api/v1/cards/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            addNewCard(desk_list_id) {
                this.$v.card_names.$each[desk_list_id].$touch()
                if (this.$v.card_names.$each[desk_list_id].$anyError) {
                    return
                }
                axios.post('/api/v1/cards', {
                    name: this.card_names[desk_list_id],
                    desk_list_id
                })
                    .then(response => {
                        this.$v.$reset()
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
            },
            getDeskLists() {
                axios.get('/api/v1/desk-lists', {
                    params: {
                        desk_id: this.deskId
                    }
                })
                    .then(response => {
                        this.desk_lists = response.data.data
                        this.desk_lists.forEach(el => {
                            this.$set(this.card_names, el.id, '')
                        })
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            },
            updateDeskList(id, name) {
                this.$v.desk_lists.$touch()
                if (this.$v.desk_lists.$anyError) {
                    return
                }
                axios.post('/api/v1/desk-lists/' + id, {
                    _method: 'PUT',
                    name
                })
                    .then(response => {
                        this.desk_list_input_id = null
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            },
            saveName() {
                this.$v.name.$touch()
                if (this.$v.name.$anyError) {
                    return
                }
                axios.post('/api/v1/desks/' + this.deskId, {_method: 'PUT', name: this.name})
                    .then(response => {
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            },
            addNewDeskList() {
                this.$v.desk_list_name.$touch()
                if (this.$v.desk_list_name.$anyError) {
                    return
                }
                axios.post('/api/v1/desk-lists', {
                    name: this.desk_list_name,
                    desk_id: this.deskId,
                })
                    .then(response => {
                        this.$v.$reset()
                        this.desk_list_name = ''
                        this.desk_lists = []
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            },
            deleteDeskList(id) {
                    axios.post('/api/v1/desk-lists/' + id, {
                        _method: 'DELETE'
                    })
                        .then(response => {
                            console.log(response)
                            this.desk_lists = []
                            this.getDeskLists()
                        })
                        .catch(error => {
                            console.log(error)
                            this.errored = true
                        })
                        .finally(() => {
                            this.loading = false
                        })
            },
            setDeskListName(value) {
                this.desk_list_name = value
                this.$v.desk_list_name.$touch()
            },
        },
        mounted() {
            axios.get('/api/v1/desks/' + this.deskId)
                .then(response => {
                    console.log(response)
                    this.name = response.data.data.name
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
            this.getDeskLists()
        },
        validations: {
            name: {
                required,
                maxLength: maxLength(255)
            },
            desk_list_name: {
                required,
                maxLength: maxLength(255)
            },
            card_names: {
                $each: {
                    required,
                    maxLength: maxLength(255)
                }
            },
            current_card: {
                name: {
                    required,
                    maxLength: maxLength(255)
                }
            },
            desk_lists: {
                $each: {
                    name: {
                        required,
                        maxLength: maxLength(255)
                    }
                }
            },
            new_task_name: {
                required,
                maxLength: maxLength(255)
            },
        }
    }
</script>
