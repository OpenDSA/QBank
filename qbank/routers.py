"""
QBank's router
Establish rules to forward user and session db requests 
to opendsa.cc.vt.edu unless we are accessing the admin.
"""
import threading

# Object to hold request data
request_cfg = threading.local()

class RouterMiddleware(object):
  """
  Set a flag if we are accessing Django admin to 
  prevent database rerouting for the auth model.  
  Remove the flag once the request has been processed.

  """

  def process_view(self, request, view_func, args, kwargs):
    if request.path.startswith('/admin'):
      request_cfg.admin = True

  def process_response(self, request, response):
    if hasattr(request_cfg, 'admin'):
      del(request_cfg.admin)
    return response

class UserSessionRouter(object):
  """
  Redirect database IO for the auth and sessions 
  models to opendsa.cc.vt.edu.

  """

  def db_for_read(self, model, **hints):
    if not hasattr(request_cfg, 'admin'):
     if model._meta.app_label == 'auth':
        return 'usersandsessions'
      elif model._meta.app_label == 'accounts':
        return 'usersandsessions'
      elif model._meta.app_label == 'sessions':
        return 'usersandsessions'
    return None

  def db_for_write(self, model, **hints):
    if not hasattr(request_cfg, 'admin'):
      if model._meta.app_label == 'auth':
        return 'usersandsessions'
      elif model._meta.app_label == 'accounts':
        return 'usersandsessions'
      elif model._meta.app_label == 'sessions':
        return 'usersandsessions'
    return None