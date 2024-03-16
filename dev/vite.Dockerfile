FROM node:lts-alpine

EXPOSE 5173
WORKDIR /app

RUN apk add --no-cache tini
ENTRYPOINT ["/sbin/tini", "--"]

CMD ["node", "node_modules/.bin/vite", "build", "--watch"]
