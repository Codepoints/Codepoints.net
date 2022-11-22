FROM node:lts-alpine

EXPOSE 5173
WORKDIR /app
CMD ["npm", "run", "dev"]
