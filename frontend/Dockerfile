# 🟢 Stage 1: Build Vue.js App
FROM node:lts-alpine AS build-stage

WORKDIR /app

# Copy package.json and install dependencies
COPY package.json package-lock.json ./
RUN npm install

# Copy all frontend source code and build
COPY . ./
RUN npm run build

# 🟢 Stage 2: Serve Vue App with Nginx
FROM nginx:alpine

WORKDIR /usr/share/nginx/html

# Copy built Vue files from previous stage
COPY --from=build-stage /app/dist .

# ✅ Ensure the correct Nginx config file is copied
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
