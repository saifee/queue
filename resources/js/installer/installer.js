const { forEach } = require("lodash");

if (document.getElementById("installer")) {
    var app = {
        data() {
            return {
                show_details: false,
                purchase_code: null,
                errors: [],
                error_message: '',
                name: null,
                company_name: null,
                email: null,
                phone: null,
                city: null,
                country: null,
                loader: false,
                permissions_page: false,
                permissions_button: false,
                set_env_button: false,
                requirments_page: false,
                verify_page: true,
                set_env_page: false,
                page: '',
                app_name: 'JL Token',
                app_timezone: null,
                database_connection: 1,
                database_host: 'localhost',
                database_port: '3306',
                database_name: 'jltoken',
                database_user_name: 'root',
                database_password: null,
                connection_exists: false,
                finished: false,
                finalEnvFile: null,
                finalStatusMessage: null,
                finalMessages: null,
                dbOutputLog: null,
                is_sub_directory: window.JLToken.is_sub_directory,
                codecanyon_username: null,
                customer_details: null,
                timezones: window.JLToken.timezones,
                host: window.JLToken.host
            }
        },

        methods: {
            verifyLicense() {
                res = this.validator('verify');

                if (res) {
                    this.loader = true;
                    const data = {
                        purchase_code: this.purchase_code,
                        username: this.codecanyon_username,
                        host: this.host
                    }
                    axios.post(window.JLToken.verify_license_url, data).then(res => {
                        if (res.data && res.data.data && res.data.data.status_code && res.data.data.status_code == 200 && res.data.data.verified) {
                            this.loader = false;
                            this.show_details = true;
                            this.error_message = null
                        } else if (res.data && res.data.data && res.data.data.status_code && res.data.data.status_code == 200 && !res.data.data.verified) {
                            this.error_message = res.data.data.message;
                        } else if (res.data && res.data.status_code && res.data.status_code == 500 && res.data.error && res.data.error == 1000) {
                            this.error_message = res.data.message;
                        }
                        else if (res.data && res.data.status_code && res.data.status_code == 422) {
                            this.error_message = (res.data.error && res.data.error.purchase_code) ? res.data.error.purchase_code[0] : (res.data.error && res.data.error.username) ? res.data.error.username[0] : 'Something Went Wrong';
                        }
                        else if (res.data && res.data.data && res.data.data.status_code) {
                            this.error_message = res.data.data.message ? res.data.data.message : 'Something Went Wrong'
                        }
                        else {
                            this.error_message = 'Something Went Wrong'
                        }
                        this.loader = false;
                    })
                        .catch(err => {
                            this.loader = false;
                            this.error_message = 'Something Went Wrong'
                        })

                }
            },

            setLicenseDetails() {
                let res = this.validator('set_license');
                if (res) {
                    this.customer_details = {
                        purchase_code: this.purchase_code,
                        name: this.name,
                        company_name: this.company_name,
                        email: this.email,
                        phone: this.phone,
                        city: this.city,
                        country: this.country,
                        username: this.codecanyon_username,
                    }

                    this.requirements();
                    // axios.post(window.JLToken.set_license_details_url, sdata).then(res => {
                    //     this.error_message = null;
                    //     if (res.data && res.data.status_code && res.data.status_code == 200) {
                    //         this.requirements()
                    //     } else if (res.data && res.data.status_code && ((res.data.status_code == 200 && !res.data.status) || res.data.status_code == 500)) {
                    //         this.error_message = 'Something Went Wrong';
                    //         this.loader = false;
                    //     } else if (res.data && res.data.status_code && res.data.status_code == 422) {
                    //         if (res.data.errors['purchase_code']) this.errors['purchase_code'] = res.data.errors['purchase_code'][0];
                    //         if (res.data.errors['name']) this.errors['name'] = res.data.errors['name'][0];
                    //         if (res.data.errors['company_name']) this.errors['company_name'] = res.data.errors['company_name'][0];
                    //         if (res.data.errors['email']) this.errors['email'] = res.data.errors['email'][0];
                    //         if (res.data.errors['phone']) this.errors['phone'] = res.data.errors['phone'][0];
                    //         if (res.data.errors['city']) this.errors['city'] = res.data.errors['city'][0];
                    //         if (res.data.errors['country']) this.errors['country'] = res.data.errors['country'][0];
                    //         if (res.data.errors['username']) this.errors['codecanyon_username'] = res.data.errors['username'][0];
                    //         this.loader = false;
                    //     }
                    //     else {
                    //         this.error_message = 'Something Went Wrong';
                    //         this.loader = false;
                    //     }
                    // })
                    //     .catch(err => {
                    //         this.error_message = 'Something Went Wrong';
                    //         this.loader = false;
                    //     })
                }
            },
            requirements() {
                this.loader = true;
                axios.get(window.JLToken.requirements_url).then(res => {
                    this.loader = false;
                    this.page = res.data.html;
                    this.verify_page = false;
                    this.requirements_page = true;
                    if (res.data.status) this.permissions_button = true;
                }).catch(err => {
                    this.loader = false;
                    this.error_message = 'Something Went Wrong'
                });
            },

            permissions() {
                this.loader = true;
                axios.get(window.JLToken.permissions_url).then(res => {
                    this.requirements_page = false;
                    this.permission_page = true;
                    this.page = res.data.html;
                    this.loader = false;
                    if (res.data.status) this.set_env_button = true;
                }).catch(err => {
                    this.loader = false;
                    this.error_message = 'Something Went Wrong'
                });
            },

            testConnection() {
                let res = this.validator('test_db');
                if (res) {
                    this.loader = true;
                    const tdata = {
                        database_connection: this.database_connection == 1 ? 'mysql' : (this.database_connection == 2 ? 'pgsql' : 'null'),
                        database_hostname: this.database_host,
                        database_port: this.database_port,
                        database_name: this.database_name,
                        database_user_name: this.database_user_name,
                        database_password: this.database_password
                    }
                    axios.post(window.JLToken.test_connection_url, tdata).then(res => {
                        if (res.data && res.data.status_code && res.data.status_code == 200) {
                            this.connection_exists = true;
                        }
                        else if (res.data && res.data.status_code && res.data.status_code == 422 && res.data.errors) {
                            this.errors['database_connection'] = res.data.errors['database_connection'][0];
                        }
                        else {
                            this.error_message = 'Could not connect to the database';
                        }
                        this.loader = false;
                    })
                        .catch(err => {
                            this.loader = false;
                            this.error_message = 'Could not connect to the database';

                        })

                }
            },

            setEnv() {
                let res = this.validator('test_db', true);
                if (res) {
                    this.loader = true;
                    const sdata = {
                        app_name: this.app_name,
                        app_timezone: this.app_timezone,
                        database_connection: this.database_connection == 1 ? 'mysql' : (this.database_connection == 2 ? 'pgsql' : 'null'),
                        database_hostname: this.database_host,
                        database_port: this.database_port,
                        database_name: this.database_name,
                        database_username: this.database_user_name,
                        database_password: this.database_password,
                        customer_details: this.customer_details
                    }
                    axios.post(window.JLToken.set_env_url, sdata).then(res => {
                        if (res.data && res.data.status_code && res.data.status_code == 422 && res.data.errors) {
                            if (res.data.errors['app_name']) this.errors['app_name'] = res.data.errors['app_name'][0];
                            if (res.data.errors['app_timezone']) this.errors['app_timezone'] = res.data.errors['app_timezone'][0];
                            if (res.data.errors['database_connection']) this.errors['database_connection'] = res.data.errors['database_connection'][0];
                            if (res.data.errors['database_hostname']) this.errors['database_host'] = res.data.errors['database_hostname'][0];
                            if (res.data.errors['database_port']) this.errors['database_port'] = res.data.errors['database_port'][0];
                            if (res.data.errors['database_name']) this.errors['database_name'] = res.data.errors['database_name'][0];
                            if (res.data.errors['database_username']) this.errors['database_user_name'] = res.data.errors['database_username'][0];
                            if (res.data.errors['database_password']) this.errors['database_password'] = res.data.errors['database_password'][0];
                            if (res.data.errors['customer_details'] || res.data.errors['purchase_code'] || res.data.errors['name'] || res.data.errors['company_name'] || res.data.errors['email'] || res.data.errors['phone'] || res.data.errors['city'] || res.data.errors['country'] || res.data.errors['username']) this.error_message = 'Something went wrong';
                        }
                        else if (res.data && res.data.status_code && res.data.status_code == 500) {
                            this.error_message = res.data.errors;
                        }
                        else if (res.data && res.data.status_code && res.data.status_code == 200) {
                            this.dbOutputLog = res.data.dbOutputLog;
                            this.finalMessages = res.data.final.finalMessages;
                            this.finalStatusMessage = res.data.final.finalStatusMessage;
                            this.finalEnvFile = res.data.final.finalEnvFile;
                            this.finished = true;
                        } else {
                            this.error_message = 'Something went wrong';
                        }
                        this.loader = false;

                    })
                        .catch(err => {
                            this.error_message = 'Something went wrong'
                            this.loader = false;
                        })

                }
            },
            valuesChangedAfterTest() {
                this.connection_exists = false;
            },

            changeDBConnection() {
                this.connection_exists = false;
                if (this.database_connection == 1) {
                    if (this.database_port == '5432') this.database_port = '3306';
                    if (this.database_user_name == 'postgres') this.database_user_name = 'root';
                } else if (this.database_connection == 2) {
                    if (this.database_port == '3306') this.database_port = 5432;
                    if (this.database_user_name == 'root') this.database_user_name = 'postgres';
                }
            },

            setEnvPage() {
                this.permission_page = false;
                this.set_env_page = true;
            },
            validator(page, final = false) {
                this.error_message = null;
                this.errors = [];
                let error = false;
                if (page == 'verify') {
                    if (!this.purchase_code) {
                        this.errors['purchase_code'] = 'Purchase Code required.';
                        error = true;
                    }
                    if (!this.codecanyon_username) {
                        this.errors['codecanyon_username'] = 'Username required.';
                        error = true;
                    }
                    if (error) return false;
                    return true;
                }
                else if (page == 'set_license') {
                    if (!this.purchase_code) {
                        this.errors['purchase_code'] = 'Purchase Code required.'
                        error = true;
                    }

                    if (!this.name) {
                        this.errors['name'] = 'Name required.';
                        error = true;
                    }
                    if (!this.company_name) {
                        this.errors['company_name'] = 'Company Name required.';
                        error = true;
                    }
                    if (!this.email) {
                        this.errors['email'] = 'Email required.';
                        error = true;
                    }
                    if (this.email && !(this.email.match(/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/))) {
                        this.errors['email'] = 'Enter Valid Email.';
                        error = true;
                    }
                    if (!this.phone) {
                        this.errors['phone'] = 'Phone required.';
                        error = true;
                    }
                    if (!this.city) {
                        this.errors['city'] = 'City required.';
                        error = true;
                    }
                    if (!this.country) {
                        this.errors['country'] = 'Country required.';
                        error = true;
                    }
                    if (error) return false;
                    return true;
                }
                else if (page == 'test_db') {
                    if (!this.database_connection) {
                        this.errors['database_connection'] = 'Database Connection required.';
                        error = true;
                    }
                    if (!this.database_host) {
                        this.errors['database_host'] = 'Database Host  required.';
                        error = true;
                    }
                    if (!this.database_port) {
                        this.errors['database_port'] = 'Database Port required.';
                        error = true;
                    }
                    if (!this.database_name) {
                        this.errors['database_name'] = 'Database Name required.';
                        error = true;
                    }
                    if (!this.database_user_name) {
                        this.errors['database_user_name'] = 'Database User Name required.';
                        error = true;
                    }
                    if (final)
                        if (!this.app_name) {
                            this.errors['app_name'] = 'App Name required.';
                            error = true;
                        }
                    if (!this.app_timezone) {
                        this.errors['app_timezone'] = 'App Timezone required.';
                        error = true;
                    }

                }

                if (error) return false;
                return true;
            }
        },

    }
    Vue.createApp(app).mount('#installer');

}




