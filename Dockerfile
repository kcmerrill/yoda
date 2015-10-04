FROM kcmerrill/base


RUN apt-get update && apt-get install -y git && apt-get clean all

RUN ln -s /yoda/yoda /usr/sbin/yoda

COPY . /yoda

CMD ["yoda"]
