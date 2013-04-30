from qtool.models import *
from django.contrib import admin

admin.site.register(Choice)
admin.site.register(ProblemTemplate)
admin.site.register(Answer)
admin.site.register(Hint)

admin.site.register(Script)

admin.site.register(Variable)

admin.site.register(CommonIntroduction)


class VariableAdmin(admin.TabularInline):
	model = Variable
	extra =0
	
class CommonIntroAdmin(admin.TabularInline):
	model = CommonIntroduction
	extra =0

class ChoiceAdmin(admin.TabularInline):
	model = Choice
	extra = 0

class ProblemTemplateAdmin(admin.TabularInline):
	model = ProblemTemplate
	extra = 0

class AnswerAdmin(admin.TabularInline):
	model = Answer
	extra = 0

class HintAdmin(admin.TabularInline):
	model = Hint
	extra = 0

class ScriptAdmin(admin.TabularInline):
	model = Script
	extra = 0

class ProblemAdmin(admin.ModelAdmin):
	inlines = [VariableAdmin, CommonIntroAdmin, ProblemTemplateAdmin, ChoiceAdmin, HintAdmin, AnswerAdmin, ScriptAdmin]

admin.site.register(Problem, ProblemAdmin)


