# GitHub Secrets Setup Guide

## ⚠️ CRITICAL: Security First!

**REVOKE YOUR GITHUB TOKEN IMMEDIATELY!**
If you shared your GitHub token, it is now public and must be revoked:
1. Go to: https://github.com/settings/tokens
2. Find and delete this token
3. Never share tokens/keys again

---

## Step-by-Step: Add GitHub Secrets

### 1. Go to Repository Settings
```
https://github.com/ervandyr2512/product-management/settings/secrets/actions
```

Or manually:
1. Go to your repository
2. Click **Settings** tab
3. Click **Secrets and variables** → **Actions**
4. Click **New repository secret**

---

## 2. Add These 5 Secrets

### Secret #1: SSH_HOST
- **Name:** `SSH_HOST`
- **Value:** `ssh.hostinger.com`
- Click "Add secret"

### Secret #2: SSH_PORT
- **Name:** `SSH_PORT`
- **Value:** `65002`
- Click "Add secret"

### Secret #3: SSH_USER
- **Name:** `SSH_USER`
- **Value:** Your Hostinger username (usually starts with `u` followed by numbers)
- Example: `u123456789`
- Find it in: Hostinger hPanel → Advanced → SSH Access
- Click "Add secret"

### Secret #4: DEPLOY_PATH
- **Name:** `DEPLOY_PATH`
- **Value:** Full path to your application on Hostinger
- Example: `/home/u123456789/teman-bicara`
- Format: `/home/[YOUR_SSH_USER]/teman-bicara`
- Click "Add secret"

### Secret #5: SSH_PRIVATE_KEY
This is the most important one. Follow these steps carefully:

#### On Your Local Machine:

**Generate SSH Key Pair:**
```bash
# Generate new SSH key for deployment
ssh-keygen -t rsa -b 4096 -f ~/.ssh/hostinger_deploy

# When prompted:
# Enter file in which to save the key: Press Enter
# Enter passphrase: Press Enter (leave empty)
# Enter same passphrase again: Press Enter
```

**Copy Private Key:**
```bash
# Display and copy the ENTIRE private key
cat ~/.ssh/hostinger_deploy

# The output will look like this:
# -----BEGIN OPENSSH PRIVATE KEY-----
# b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAACFwAAAAdzc2gtcn
# ... (many lines) ...
# -----END OPENSSH PRIVATE KEY-----

# Copy ALL of it including the BEGIN and END lines
```

**Add to GitHub:**
- **Name:** `SSH_PRIVATE_KEY`
- **Value:** Paste the entire private key (all lines)
- Click "Add secret"

---

## 3. Add Public Key to Hostinger

**Copy Public Key:**
```bash
# Display public key
cat ~/.ssh/hostinger_deploy.pub

# Output will be one line starting with 'ssh-rsa'
# Copy the entire line
```

**Add to Hostinger:**
1. Login to Hostinger hPanel: https://hpanel.hostinger.com
2. Go to **Advanced** → **SSH Access**
3. Enable SSH if not already enabled
4. Click **Add SSH Key** or **Manage SSH Keys**
5. Paste your public key
6. Give it a name: "GitHub Actions Deploy"
7. Click **Add** or **Save**

---

## 4. Verify Secrets Are Added

Go back to:
```
https://github.com/ervandyr2512/product-management/settings/secrets/actions
```

You should see 5 secrets listed:
- ✅ SSH_HOST
- ✅ SSH_PORT
- ✅ SSH_USER
- ✅ SSH_PRIVATE_KEY
- ✅ DEPLOY_PATH

---

## 5. Test the Deployment

Make a small change to trigger the workflow:

```bash
# Make a test commit
echo "" >> README.md
git add README.md
git commit -m "Test auto-deployment workflow"
git push origin main
```

Then watch the deployment:
```
https://github.com/ervandyr2512/product-management/actions
```

You should see:
- ✅ "Test auto-deployment workflow" workflow running
- ✅ All steps passing (green checkmarks)
- ✅ Deployment completed successfully

---

## Troubleshooting

### Error: "Host key verification failed"
**Solution:** Add Hostinger to known_hosts in the workflow.

Edit `.github/workflows/deploy.yml` and add before the SSH action:
```yaml
- name: Add Hostinger to known hosts
  run: |
    mkdir -p ~/.ssh
    ssh-keyscan -p 65002 -H ssh.hostinger.com >> ~/.ssh/known_hosts
```

### Error: "Permission denied (publickey)"
**Solutions:**
1. Check that you copied the ENTIRE private key to SSH_PRIVATE_KEY
2. Verify the public key is added to Hostinger correctly
3. Make sure the key has no passphrase

### Error: "composer: command not found"
**Solution:** Composer might not be in PATH on Hostinger. Update the workflow to use full path:
```bash
/usr/bin/composer install --no-dev --optimize-autoloader
```

### Error: "npm: command not found" or "Build failed"
**Solution:** The build step happens on GitHub Actions server, not Hostinger. If this fails:
1. Check `npm run build` works locally
2. Commit `package-lock.json` to repository
3. Make sure all dependencies are in `package.json`

---

## Security Best Practices

✅ **DO:**
- Use separate SSH keys for deployment (not your personal key)
- Keep private keys secret
- Use GitHub Secrets for sensitive data
- Regularly rotate SSH keys
- Enable 2FA on GitHub

❌ **DON'T:**
- Share private keys, tokens, or passwords
- Commit secrets to repository
- Use the same key for multiple servers
- Share tokens in chat, screenshots, or videos

---

## Need Help?

If the workflow still fails after following this guide:
1. Go to: https://github.com/ervandyr2512/product-management/actions
2. Click the failed workflow
3. Take a screenshot of the error
4. Share the error message (NOT any keys/tokens)

---

*Last Updated: January 14, 2026*
