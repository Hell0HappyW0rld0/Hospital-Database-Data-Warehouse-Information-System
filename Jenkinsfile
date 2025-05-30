pipeline {
    agent any

    environment {
        // Define environment variables
        DIRECTORY_PATH = '/code'
        TESTING_ENVIRONMENT = 'staging-environment'
        PRODUCTION_ENVIRONMENT = 'production-environment'
        DEPLOY_DIR = '/Users/thech/Documents/Deakin/SIT753/Week 7, 8, 9/7.3HD/Project Reports/COMP4990-main/deploy'
        DOCKER_REG = 'hellohappyw0rld/hospital-app'
    }

    stages {
        // Stage 0: Checkout Github repo
        stage('Checkout') {
            steps {
                // Checkout the latest code from Git
                checkout scm
            }
        }

        // Stage 1: Build aertifact (ZIP & Docker image)
        stage('Build') {
            steps {
                script {
                    echo "Creating a build artifact (ZIP) for PHP project"
                    // Clean up temporary and unnecessary files before any build
                    echo "Cleaning up unnecessary files..."
                    sh "rm -rf ${DEPLOY_DIR}/production"
                    sh "rm -rf ${DEPLOY_DIR}/staging"
                    sh "rm -rf build && mkdir build"
                    sh "rm -rf .gitignore build/tmp build/*.log"
                    sh "rm -f artifact.zip"

                    // Create directory and copy the files
                    sh "rm -rf build && mkdir build"
                    sh "cp -r * build/"
                    // Create ZIP file of the build directory
                    sh "cd build && zip -r ../artifact.zip ."
                    // Archive the aertifact
                    archiveArtifacts artifacts: "artifact.zip", fingerprint: true
                }
                timestamps {
                    // Build, tag, and push the Docker image
                    script {
                        sh "docker build --force-rm -t ${DOCKER_REG}:${BUILD_NUMBER} ."
                        withCredentials([string(credentialsId: 'Docker_Hub', variable: 'Docker_Hub')]) {
                        sh """
                            docker login -u $DOCKER_USER -p $DOCKER_PWD ${DOCKER_REG}
                            docker push ${DOCKER_REG}:${BUILD_NUMBER}
                        """
                        }
                    }
                }
            }
        }

        // Stage 2: Unit and Integration Tests (PHPUnit)
        stage('Unit and Integration Tests') {
            steps {
                script {
                    // Run unit and integration tests using PHPUnit
                    echo "Running unit and integration tests"
                    // Install dependencies and ensure that tests exists
                    sh "composer install"
                    sh "vendor/bin/phpunit tests"
                }
                // This will run unit tests with JUnit coverage
                script {
                    sh "composer install --no-interaction"
                    sh "vendor/bin/phpunit --log-junit build/logs/junit.xml --coverage-clover build/logs/clover.xml"
                    junit 'build/logs/junit.xml'
 
                    // Test the database for integration
                    sh "docker-compose -f docker-compose.test.yml up -d db"
                    sh "vendor/bin/phpunit --testsuite integration"
                    sh "docker-compose -f docker-compose.test.yml down"
 
                    // Minimum 80% coverage
                    def cov = org.jenkinsci.plugins.clover.CloverPublisher.getCoveragePercent('build/logs/clover.xml')
                    if (cov < 80) error "Coverage too low: ${cov}%"
                }
            }
        }

        // Stage 3: Code Analysis using SonarQube
        stage('Code Analysis') {
            steps {
                script {
                    echo "Running SonarQube analysis"
                    // Grab credential in Jenkins credential
                    withCredentials([string(credentialsId: 'SONARQUBE_TOKEN', variable: 'SONARQUBE_TOKEN')]) {
                        sh """
                            sonar-scanner \
                            -Dsonar.projectKey=my-php-project \
                            -Dsonar.sources=. \
                            -Dsonar.host.url=http://localhost:9000 \
                            -Dsonar.login=${SONARQUBE_TOKEN}
                        """
                    }
                    // Force quality check Sonar
                    timeout(time: 2, unit: 'MINUTES') {
                        waitForQualityGate abortPipeline: true
                    }
                }
            }
        }

        // Stage 4: Security Scan using OWASP Dependency Check
        stage('Security Scan') {
            steps {
                script {
                    echo "Running security scan on PHP dependencies with OWASP Dependency-Check"
                    // Scan for known vulnerabilities in the dependency
                    sh "/opt/homebrew/bin/dependency-check --project PHP-Project --scan . --format HTML --out dependency-check-report"
                    // Archive the report
                    archiveArtifacts artifacts: "dependency-check-report/*.html", allowEmptyArchive: true
                }
            }
        }

        // Stage 5: Deploy to Staging
        stage('Deploy to Staging') {
            steps {
                script {
                    echo "Deploying to testing environment: ${env.TESTING_ENVIRONMENT}"
                    // This will simulate the deployment to staging by unpacking the ZIP artifact
                    sh "rm -rf \"${DEPLOY_DIR}/staging\" && mkdir -p \"${DEPLOY_DIR}/staging\""
                    sh "unzip artifact.zip -d \"${DEPLOY_DIR}/staging\""
                }
                // Deploy through Docker Compose
                script {
                    sh "docker-compose -f docker-compose.staging.yml pull"
                    sh "docker-compose -f docker-compose.staging.yml up -d --build"
                }
            }
        }

        // Stage 6: Integration Tests on Staging (manual approval needed)
        stage('Integration Tests on Staging') {
            steps {
                script {
                    echo "Waiting for manual approval..."
                    // Add timer and message for deployment approval
                    input message: "Approve deployment to production?", ok: "Deploy", timeout: 1 * 60 * 60, timeoutMessage: "Approval timed out"
                    echo "Approval received, proceeding with deployment."
                }
            }
        }

        // Stage 7: Deploy to Production
        stage('Deploy to Production') {
            steps {
                script {
                    echo "Deploying to production environment: ${env.PRODUCTION_ENVIRONMENT}"
                    // This will simulate the deployment to deployment by unpacking the ZIP artifact
                    sh "rm -rf \"${DEPLOY_DIR}/production\" && mkdir -p \"${DEPLOY_DIR}/production\""
                    sh "unzip artifact.zip -d \"${DEPLOY_DIR}/production\""
                }
            }
        }

        // Stage 8: Release to Production (Github Releases)
        stage('Release') {
            steps {
                script {
                    echo "Releasing application to GitHub"

                    dir("${DEPLOY_DIR}") {
                        // Clean untracked files
                        sh 'git clean -fd'
                        // Show status and tag release
                        sh 'git status'
                        sh 'git tag -a v${BUILD_NUMBER} -m "Release v${BUILD_NUMBER}"'
                        // Force push instead (due to previous conflicts with existing tags)
                        sh 'git push --force origin --tags'
                        sh 'echo "Release application to production"'
                    }

                    // Create GitHub release with changelog
                    sh "gh auth login --with-token < ~/.github_token"
                    sh "gh release create v${BUILD_NUMBER} --notes-file CHANGELOG.md"
                }
            }
        }

        // Stage 9: Monitoring and Alerting
        stage('Monitoring and Alerting') {
            steps {
                script {
                    echo "Configuring monitoring for production"
                    // Integration with monitoring tools - New Relic version and validation and check logs
                    withCredentials([string(credentialsId: 'NRIA_LICENSE_KEY', variable: 'NRIA_LICENSE_KEY')]) {
                        sh """
                            newrelic-infra --version
                            export NRIA_LICENSE_KEY=${NRIA_LICENSE_KEY}
                            newrelic-infra --validate
                        """
                    }
                    echo "New Relic is running"
                }
            }
        }
    }

    // Added post message to specify success and failure to execute the pipeline
    post {
        success {
            echo "Pipeline executed successfully"
        }
        failure {
            echo "Pipeline failed. Please check the logs for errors."
        }
    }
}
