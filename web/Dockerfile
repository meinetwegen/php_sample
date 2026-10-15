# Build context: ./web  (this folder contains the Vue app in ./web and the nginx config)
FROM node:20-alpine AS base
WORKDIR /app
ENV CYPRESS_INSTALL_BINARY=0
COPY web/package*.json ./
RUN npm ci

# docker compose -f docker-compose.yml -f docker-compose.dev.yml up  -> dev server with hot reload
FROM base AS dev
COPY web/ ./
EXPOSE 8080
CMD ["npm", "run", "serve"]

FROM base AS build
COPY web/ ./
RUN npm run build

# default target: static files behind nginx
FROM nginx:1.27-alpine AS production
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html
EXPOSE 80
