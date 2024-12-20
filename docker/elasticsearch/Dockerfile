FROM docker.elastic.co/elasticsearch/elasticsearch:7.17.7

RUN chown -R 1000:1000 /usr/share/elasticsearch/data
RUN chown -R 1000:1000 ./data/elasticsearch

EXPOSE 9200
