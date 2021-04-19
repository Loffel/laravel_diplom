<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Ваши продукты</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-block btn-success" @click="createModal"><i class="fas fa-plus"></i> Добавить товар</button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Название</th>
                                        <th>Магазин</th>
                                        <th>ID из магазина</th>
                                        <th>Комментарий</th>
                                        <th>Дата добавления</th>
                                        <th class="text-center">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="product in products" :key="product.id">
                                        <td>{{ product.id }}</td>
                                        <td><router-link :to="{ path: '/products/' + product.id }">{{ product.title }}</router-link></td>
                                        <td><a :href="product.shop.url" target="_blank">{{ product.shop.title }}</a></td>
                                        <td><a :href="product.shop.url + product.shop.url_product.replace('*', product.product_uid)" target="_blank">{{ product.product_uid }}</a></td>
                                        <td>{{ product.comment }}</td>
                                        <td>{{ product.created_at | myDate }}</td>
                                        <td class="project-actions text-right">
                                            <router-link :to="{ path: '/products/' + product.id }" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                                Прогноз
                                            </router-link>
                                            <a href="#" class="btn btn-info btn-sm" @click="editModal(product)">
                                                <i class="fas fa-pencil-alt"></i>
                                                Редактировать
                                            </a>
                                            <a href="#" class="btn btn-danger btn-sm" @click="deleteProduct(product.id)">
                                                <i class="fas fa-trash"></i>
                                                Удалить
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

        <div class="modal fade" id="modal-add-product">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 v-show="editMode" class="modal-title">Обновить товар</h4>
                        <h4 v-show="!editMode" class="modal-title">Добавить товар</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="add-product" @submit.prevent="editMode ? updateProduct() : createProduct()">
                            <div class="form-group">
                                <label>Название товара</label>
                                <input v-model="form.title" type="text" name="title" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('title') }" required>
                                <has-error :form="form" field="title"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Площадка</label>
                                <select :disabled="editMode" v-model="form.shop_id" name="shop_id" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('shop_id') }" required>
                                    <option value="">Выберите магазин</option>
                                    <option v-for="shop in shops" :key="shop.id" :value="shop.id">{{ shop.title }}</option>
                                </select>
                                <has-error :form="form" field="product_uid"></has-error>
                            </div>

                            <div class="form-group">
                                <label>ID продукта</label>
                                <input :disabled="editMode" v-model="form.product_uid" type="text" name="product_uid" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('product_uid') }" required>
                                <has-error :form="form" field="product_uid"></has-error>
                            </div>

                            <div class="form-group">
                                <label>Комментарий</label>
                                <input v-model="form.comment" type="text" name="comment" class="form-control"
                                    :class="{ 'is-invalid': form.errors.has('comment') }">
                                <has-error :form="form" field="comment"></has-error>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer justify-content-end">
                        <button v-show="editMode" type="submit" form="add-product" class="btn btn-success">Обновить</button>
                        <button v-show="!editMode" type="submit" form="add-product" class="btn btn-primary">Добавить</button>
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
                products: {},
                shops: {},
                form: new Form({
                    id: '',
                    title: '',
                    shop_id: '',
                    product_uid: '',
                    comment: ''
                })
            }
        },
        methods: {
            createModal(){
                this.editMode = false;
                this.form.reset();
                $('#modal-add-product').modal('show');
            },
            editModal(product){
                this.editMode = true;
                this.form.reset();
                $('#modal-add-product').modal('show');
                this.form.fill(product);
            },
            deleteProduct(id){
                swal.fire({
                    title: 'Вы уверены?',
                    text: "Это действие нельзя будет отменить!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Да, удалить продукт!'
                    }).then((result) => {
                        if (result.value) {
                            this.form.delete('api/product/' + id).then(() => {
                                Fire.$emit('AfterEvent');
                                swal.fire('Удалено!', 'Продукт был удалён.', 'success');
                            }).catch(() => {
                                swal.fire('Ошибка!', 'Ошибка удаления продукта.', 'error');
                            });
                        }
                    });
            },
            loadProducts(){
                axios.get('api/product').then(({ data }) => (this.products = data.data));
            },
            loadShops(){
                axios.get('api/shop').then(({ data }) => (this.shops = data));
            },
            createProduct(){
                this.$Progress.start();

                this.form.post('api/product')
                    .then(() => {
                        Fire.$emit('AfterEvent');
                        toast.fire({
                            icon: 'success',
                            title: 'Продукт добавлен!'
                        });
                        $('#modal-add-product').modal('hide');
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
            updateProduct(){
                this.$Progress.start();
                this.form.put('api/product/' + this.form.id)
                    .then(() => {
                        Fire.$emit('AfterEvent');
                        toast.fire({
                            icon: 'success',
                            title: 'Продукт обновлён!'
                        });
                        $('#modal-add-product').modal('hide');
                        this.$Progress.finish();
                    })
                    .catch(() => {
                        this.$Progress.fail();
                    });
            }
        },
        created() {
            this.loadShops();
            this.loadProducts();
            Fire.$on('AfterEvent', () => {
                this.loadProducts();
            });
        }
    }

</script>
