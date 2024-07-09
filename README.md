# Git instructions

## Fetching latest `main` code

1. `git checkout main` Goes to the main branch
2. `git fetch origin` Fetches the possible code upgrades from the repo
3. `git pull` pulls the latest code

## Creating a feature or upgrading existing code

1. From the `main` branch you type `git checkout -b feat/demo`
2. After making some code changes, you need to commit them `git add .` -> `git commit -m "some message"`
3. If needed copy the command like this: `git push --set-upstream origin feat/demo`
4. Go to GitHub and open new pull request

## Updating existing branch

1. After making some code changes, you need to commit them `git add .` -> `git commit -m "some message"`
2. Pushing the changes to remote repo branch `git push --set-upstream origin feat/demo`

## Updating the project in the server

1. Login via SSH
2. Navigate to project directory `/var/www/staging`
3. `git fetch origin` (Use your github keys - name: {token} pass: {token})
4. `git pull` (Use your github keys - name: {token} pass: {token})
5. Run: `sh run.staging.sh` script from the directory

## Local dev

-   `npm run dev` makes CSS updates
