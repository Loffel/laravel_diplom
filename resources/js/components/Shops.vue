<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Магазины</h3>

                            <div class="card-tools">
                                <!-- <button type="button" class="btn btn-block btn-success" @click="createModal"><i class="fas fa-plus"></i> Добавить товар</button> -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>URL</th>
                                        <th>URL продукта</th>
                                        <th>Шаблон для парсинга</th>
                                        <th>Валюта</th>
                                        <th>Дата добавления</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="shop in shops" :key="shop.id">
                                        <td>{{ shop.id }}</td>
                                        <td>{{ shop.title }}</td>
                                        <td><a :href="shop.url">{{ shop.url }}</a></td>
                                        <td>{{ shop.url_product }}</td>
                                        <td>{{ shop.template }}</td>
                                        <td>{{ shop.currency }}</td>
                                        <td>{{ shop.created_at | myDate }}</td>
                                        <td>
                                            <a href="#" @click="editModal(shop)">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            /
                                            <a href="#" @click="deleteShop(shop.id)">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->

        <div class="modal fade" id="modal-add-shop">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 v-show="editMode" class="modal-title">Обновить магазин</h4>
                        <h4 v-show="!editMode" class="modal-title">Добавить магазин</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="add-shop" @submit.prevent="editMode ? updateShop() : createShop()">
                            <div class="form-group">
                                <label>Название магазина</label>
                                <input v-model="form.title" type="text" name="title" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('title') }" required>
                                <has-error :form="form" field="title"></has-error>
                            </div>

                            <div class="form-group">
                                <label>URL магазина</label>
                                <input v-model="form.url" type="text" name="url" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('url') }" required>
                                <has-error :form="form" field="url"></has-error>
                            </div>

                            <div class="form-group">
                                <label>URL продукта</label>
                                <input v-model="form.url_product" type="text" name="url_product" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('url_product') }" required>
                                <has-error :form="form" field="url_product"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Шаблон парсера</label>
                                <input v-model="form.template" type="text" name="template" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('template') }">
                                <has-error :form="form" field="template"></has-error>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button v-show="editMode" type="submit" form="add-shop" class="btn btn-success">Обновить</button>
                        <button v-show="!editMode" type="submit" form="add-shop" class="btn btn-primary">Добавить</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </section>
</template>

<script>
    export default {
        props: ['auth_user'],
        data() {
            return {
                editMode: false,
                shops: {},
                form: new Form({
                    id: '',
                    title: '',
                    url: '',
                    url_product: '',
                    template: ''
                })
            }
        },
        methods: {
            createModal(){
                this.editMode = false;
                this.form.reset();
                $('#modal-add-shop').modal('show');
            },
            editModal(shop){
                this.editMode = true;
                this.form.reset();
                $('#modal-add-shop').modal('show');
                this.form.fill(shop);
            },
            deleteShop(id){
                swal.fire({
                    title: 'Вы уверены?',
                    text: "Это действие нельзя будет отменить!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да, удалить магазин!'
                    }).then((result) => {
                        if (result.value) {
                            this.form.delete('api/shop/' + id).then(() => {
                                Fire.$emit('AfterEvent');
                                swal.fire('Удалено!', 'Магазин был удалён.', 'success');
                            }).catch(() => {
                                swal.fire('Ошибка!', 'Ошибка удаления магазина.', 'error');
                            });
                        }
                    });
            },
            loadShops(){
                axios.get('api/shop').then(({ data }) => (this.shops = data));
            },
            createShop(){
                this.$Progress.start();

                this.form.post('api/shop')
                    .then(() => {
                        Fire.$emit('AfterEvent');
                        toast.fire({
                            icon: 'success',
                            title: 'Магазин добавлен!'
                        });
                        $('#modal-add-shop').modal('hide');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        toast.fire({
                            icon: 'error',
                            title: 'Ошибка добавления!'
                        });
                        this.$Progress.fail();
                    });
            },
            updateShop(){
                this.$Progress.start();
                this.form.put('api/shop/' + this.form.id)
                    .then(() => {
                        Fire.$emit('AfterEvent');
                        toast.fire({
                            icon: 'success',
                            title: 'Магазин обновлён!'
                        });
                        $('#modal-add-shop').modal('hide');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            }
        },
        created() {
            this.loadShops();
            Fire.$on('AfterEvent', () => {
                this.loadShops();
            });
        }
    }

</script>