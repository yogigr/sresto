<template>
     <form @submit.prevent="login">
        <div class="form-group has-feedback">
            <input type="email" v-model="loginData.email" class="form-control" placeholder="Email">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input type="password" v-model="loginData.password" class="form-control" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary 
            btn-block btn-flat" :disabled="isSubmitting">Sign In</button>
        </div>
      </form>
</template>

<script>
    export default {
        data() {
            return {
                loginData: {
                    email: '',
                    password: ''
                },
                isSubmitting: false
            }
        },

        methods: {
            login: function(){
                if (this.loginData.email != '' && this.loginData.password != '') {
                    this.isSubmitting = true;

                    axios.post('/login', {email: this.loginData.email, password: this.loginData.password})
                    .then(response => {
                        if (response.status == 200) {
                            this.notify('success', response.data.message)
                            setTimeout(function(){
                                window.location.replace(response.data.url);
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        var errors = error.response.data.errors;
                        $.each(errors, function(i, v){
                            new Noty({
                                text: v[0],
                                type: 'error',
                                timeout: 2000,
                                layout: 'bottomRight'
                            }).show();
                        });
                        this.isSubmitting = false
                        this.loginData.password = ''
                    })
                }
            },

            notify(type, text){
                new Noty({
                    text: text,
                    type: type,
                    timeout: 2000,
                    layout: 'bottomRight'
                }).show();
            }
        }
    }
</script>
