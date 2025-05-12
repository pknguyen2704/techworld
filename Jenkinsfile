// Jenkinsfile
pipeline {
    // Chạy trên bất kỳ agent nào có label 'docker' và 'kubectl' (hoặc chỉ 'agent any' nếu agent mặc định có đủ)
    agent any
    // agent { label 'docker && kubectl' }

    environment {
        // --- THAY THẾ CÁC GIÁ TRỊ SAU ---
        DOCKER_REGISTRY             = "pknguyen2704" // Hoặc URL registry khác (vd: gcr.io/your-project-id)
        DOCKER_CREDENTIAL_ID        = "docker-regestry-credential" // ID credential Docker trong Jenkins
        KUBE_CONFIG_CREDENTIAL_ID   = "kubernetes-cluster-credentials" // ID credential K8s trong Jenkins
        IMAGE_NAME_BASE             = "${DOCKER_REGISTRY}/techworld-php-fpm" // Tên image PHP-FPM base
        IMAGE_NAME_APP              = "${DOCKER_REGISTRY}/techworld-magento-app" // Tên image ứng dụng cuối cùng
        K8S_NAMESPACE               = "magento-prod" // Namespace K8s để deploy
        // --- KẾT THÚC PHẦN THAY THẾ ---

        // Sử dụng Build ID của Jenkins làm tag duy nhất cho image
        IMAGE_TAG                   = "${env.BUILD_ID}"
        // Container name trong K8s deployment PHP-FPM (kiểm tra file 20-php-fpm-deployment.yaml)
        PHP_FPM_CONTAINER_NAME      = "php-fpm-container"
        // Container name trong K8s deployment Nginx (kiểm tra file 22-nginx-deployment.yaml)
        NGINX_CONTAINER_NAME        = "nginx-container"
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'master', credentialsId: 'github-credential', url: 'https://github.com/pknguyen2704/techworld.git' // Thay URL nếu cần
                script {
                    // Hiển thị commit hash để tiện theo dõi
                    def commitHash = sh(script: 'git rev-parse --short HEAD', returnStdout: true).trim()
                    env.COMMIT_HASH = commitHash // Lưu lại để dùng nếu muốn tag image bằng commit hash
                }
            }
        }

        // (Tùy chọn) Stage: Build Base Image PHP-FPM
        // Chỉ nên chạy nếu Dockerfile base có thay đổi. Có thể tạo job riêng cho việc này.
        // stage('Build Base Image') {
        //     when { changeSet "docker/php7.4-fpm/**" } // Chỉ chạy nếu có thay đổi trong thư mục base
        //     steps {
        //         script {
        //             def baseImage = docker.build("${IMAGE_NAME_BASE}:${IMAGE_TAG}", "-f docker/php7.4-fpm/Dockerfile .")
        //             docker.withRegistry("https://${DOCKER_REGISTRY}", DOCKER_CREDENTIAL_ID) {
        //                 baseImage.push()
        //                 // Có thể push thêm tag 'latest'
        //                 // baseImage.push('latest')
        //             }
        //         }
        //     }
        // }

        stage('Build Magento App Image') {
            steps {
                script {
                    // Build image ứng dụng sử dụng Dockerfile.app và image base
                    // Truyền tên image base vào làm build argument
                    def appImage = docker.build(
                        "${IMAGE_NAME_APP}:${IMAGE_TAG}",
                        "--build-arg BASE_IMAGE=${IMAGE_NAME_BASE}:latest -f Dockerfile ." // Sử dụng base image tag 'latest'
                    )
                }
            }
        }

        stage('Push Magento App Image') {
            steps {
                script {
                    // Đăng nhập và đẩy image lên registry
                    docker.withRegistry("https://${DOCKER_REGISTRY}", DOCKER_CREDENTIAL_ID) {
                        def appImage = docker.image("${IMAGE_NAME_APP}:${IMAGE_TAG}")
                        appImage.push() // Push tag BUILD_ID
                        appImage.push('latest') // Push thêm tag 'latest'
                    }
                }
            }
        }

        // (Tùy chọn) Stage: Run Tests
        // stage('Run Tests') {
        //     steps {
        //         // Thêm các bước chạy unit test, integration test ở đây
        //         // Ví dụ: sh 'vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist'
        //     }
        // }

        stage('Deploy to Kubernetes') {
            steps {
                // Sử dụng credential K8s đã cấu hình
                withKubeConfig([credentialsId: KUBE_CONFIG_CREDENTIAL_ID]) {
                    // 1. Apply tất cả các manifest trong thư mục k8s/
                    //    Điều này đảm bảo các PVC, ConfigMap, Secret, Services, StatefulSets được tạo/cập nhật
                    //    Lệnh apply an toàn để chạy lại nhiều lần.
                    sh "kubectl apply -f k8s/ -n ${K8S_NAMESPACE}"

                    // 2. Cập nhật image cho Deployment PHP-FPM
                    //    Sử dụng tên container đã định nghĩa trong environment
                    sh "kubectl set image deployment/magento-php-fpm ${PHP_FPM_CONTAINER_NAME}=${IMAGE_NAME_APP}:${IMAGE_TAG} -n ${K8S_NAMESPACE}"

                    // 3. (Tùy chọn) Cập nhật image cho Deployment Nginx nếu image Nginx cũng thay đổi
                    // sh "kubectl set image deployment/magento-nginx ${NGINX_CONTAINER_NAME}=<nginx-image-name>:<tag> -n ${K8S_NAMESPACE}"

                    // 4. Chờ Deployment PHP-FPM hoàn tất rollout
                    sh "kubectl rollout status deployment/magento-php-fpm -n ${K8S_NAMESPACE} --timeout=5m"

                    // 5. Chờ Deployment Nginx hoàn tất rollout
                    sh "kubectl rollout status deployment/magento-nginx -n ${K8S_NAMESPACE} --timeout=2m"
                }
            }
        }
    } // end stages

    post {
        always {
            // Luôn luôn dọn dẹp workspace sau khi build
            cleanWs()
        }
        success {
            // Gửi thông báo thành công lên Slack
            slackSend channel: '#devops-alerts', // Thay kênh Slack
                      color: 'good',
                      message: "SUCCESS: Job '${env.JOB_NAME}' [${env.BUILD_NUMBER}] - Commit ${env.COMMIT_HASH} deployed to ${K8S_NAMESPACE}. URL: ${env.BUILD_URL}"
        }
        failure {
            // Gửi thông báo thất bại lên Slack
            slackSend channel: '#devops-alerts', // Thay kênh Slack
                      color: 'danger',
                      message: "FAILED: Job '${env.JOB_NAME}' [${env.BUILD_NUMBER}] - Commit ${env.COMMIT_HASH}. Check console output: ${env.BUILD_URL}"
        }
    } // end post
} // end pipeline