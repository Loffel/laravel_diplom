<template>
    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Регистрация</p>

            <form  @submit.prevent="register()">
                <div class="input-group mb-3">
                    <input v-model="form.name" type="text" name="name" class="form-control" 
                    :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Имя" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="name"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input v-model="form.email" type="email" name="email" class="form-control" 
                    :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="email"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input v-model="form.password" type="password" name="password" class="form-control" 
                    :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Пароль" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="password"></has-error>
                </div>
                <div class="input-group mb-3">
                    <input v-model="form.password_confirmation" type="password" name="password_confirmation" class="form-control" 
                    :class="{ 'is-invalid': form.errors.has('password_confirmation') }" placeholder="Повторите пароль" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    <has-error :form="form" field="password_confirmation"></has-error>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="icheck-primary">
                            <input v-model="form.agree" type="checkbox" id="agreeTerms" name="agree" :class="{ 'is-invalid': form.errors.has('agree') }" required>
                            <label for="agreeTerms">
                                Я согласен с <a href="#">правилами</a>
                            </label>
                            <has-error :form="form" field="agree"></has-error>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-block">Регистрация</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <router-link to="/login" href="#" class="text-center">Авторизоваться</router-link>
        </div>
        <!-- /.login-card-body -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: new Form({
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    agree: false
                })
            }
        },
        methods: {
            register() {
                this.$Progress.start();

                this.form.post('register')
                    .then((response) => {
                        this.$Progress.finish();
                        window.location.href = '/products';
                    })
                    .catch(() => {
                        toast.fire({
                            icon: 'error',
                            title: 'Произошла ошибка!'
                        });
                        this.$Progress.fail();
                    });
            }
        },
        created() {

        }
    }

</script>
