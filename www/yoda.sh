docker pull sequenceiq/socat
docker kill docker-http
docker kill automagicproxy
docker kill yoda
docker rm -f docker-http
docker rm -f automagicproxy
docker rm -f yoda
docker run -d -p 2375:2375 -v /var/run/docker.sock:/var/run/docker.sock --name=docker-http sequenceiq/socat
docker run -d -p 80:80 --name=automagicproxy kcmerrill/automagicproxy
docker run -d -h ${HOSTNAME} -P -v $PWD:/var/www/html --name=yoda kcmerrill/base
