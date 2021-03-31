FROM openjdk:jdk
COPY mockserver.jar /app/mockserver.jar
WORKDIR /app

#RUN curl http://search.maven.org/remotecontent?filepath=org/mock-server/mockserver-netty/5.11.2/mockserver-netty-5.11.2-jar-with-dependencies.jar -o mockserver.jar -s -L

ENTRYPOINT ["sh", "-c", "java -jar mockserver.jar -serverPort 1080 -logLevel FINEST"]