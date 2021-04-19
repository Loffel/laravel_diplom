<template>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Авторизуйтесь, чтобы пользоваться сервисом</p>

            <form  @submit.prevent="login()">
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
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input v-model="form.remember" type="checkbox" name="remember" id="remember">
                            <label for="remember">
                                Запомнить меня
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Войти</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <p class="mb-1">
                <a href="#">Забыли пароль?</a>
            </p>
            <p class="mb-0">
                <router-link to="/register" href="#" class="text-center">Регистрация</router-link>
            </p>
        </div>
        <!-- /.login-card-body -->
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: new Form({
                    email: '',
                    password: '',
                    remember: false
                })
            }
        },
        methods: {
            login() {
                this.$Progress.start();

                this.form.post('login')
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
